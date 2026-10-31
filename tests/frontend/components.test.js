import { describe, it, expect, vi } from 'vitest';
import fs from 'node:fs';
import path from 'node:path';
import { parse, compileScript, compileTemplate } from '@vue/compiler-sfc';
import { resolveConfig } from 'vite';
import viteConfig from '../../vite.config.mjs';

function componentFiles(directory) {
    return fs.readdirSync(directory, { withFileTypes: true }).flatMap(entry => {
        const file = path.join(directory, entry.name);
        return entry.isDirectory() ? componentFiles(file) : file.endsWith('.vue') ? [file] : [];
    });
}

describe('migration coverage', () => {
    it('resolves component tests in CI while retaining Laravel for production builds', async () => {
        vi.stubEnv('CI', 'true');
        const testing = await resolveConfig({ mode: 'test' }, 'serve');
        expect(testing.plugins.some(plugin => plugin.name === 'vite:vue')).toBe(true);
        expect(testing.plugins.some(plugin => plugin.name === 'laravel')).toBe(false);

        vi.stubEnv('VITEST', '');
        const production = await resolveConfig({ mode: 'production' }, 'build');
        expect(production.plugins.some(plugin => plugin.name === 'laravel')).toBe(true);
    });

    it('compiles every application SFC with the native Vue 3 compiler', () => {
        const files = componentFiles('resources/js');
        expect(files.length).toBeGreaterThan(60);
        for (const file of files) {
            const source = fs.readFileSync(file, 'utf8');
            const { descriptor, errors } = parse(source, { filename: file });
            expect(errors, file).toEqual([]);
            if (descriptor.script) expect(() => compileScript(descriptor, { id: file }), file).not.toThrow();
            if (descriptor.template) {
                const compiled = compileTemplate({ source: descriptor.template.content, filename: file, id: file });
                expect(compiled.errors, file).toEqual([]);
                expect(descriptor.template.content, file).not.toMatch(/\|\s*(formatNumber|formatPrice|formatDate)/);
            }
        }
    });
    it('uses one native Vue 3 runtime without Vue 2-only dependencies', () => {
        const manifest = JSON.parse(fs.readFileSync('package.json', 'utf8'));
        expect(manifest.dependencies.vue).toMatch(/^\^3\./);
        for (const dependency of ['@vue/compat', 'laravel-mix', 'vue-template-compiler', 'vue2-google-maps', 'vue-toasted', 'vue-content-loading', 'vuex']) {
            expect(manifest.dependencies[dependency] || manifest.devDependencies[dependency]).toBeUndefined();
        }
    });
    it('keeps existing public MIX settings as a fallback without exposing private settings', async () => {
        vi.stubEnv('VITE_APP_URL', '');
        vi.stubEnv('MIX_APP_URL', 'https://legacy.example.test');
        vi.stubEnv('MIX_VUE_APP_TOKEN', 'private-must-not-ship');
        const config = await viteConfig({ mode: 'test' });
        expect(JSON.parse(config.define['import.meta.env.VITE_APP_URL'])).toBe('https://legacy.example.test');
        expect(JSON.stringify(config.define)).not.toContain('private-must-not-ship');
        vi.stubEnv('VITE_APP_URL', 'https://vite.example.test');
        const preferred = await viteConfig({ mode: 'test' });
        expect(JSON.parse(preferred.define['import.meta.env.VITE_APP_URL'])).toBe('https://vite.example.test');
    });
});
