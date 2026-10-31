<template>

</template>
<script>
export default {
    name: 'AutoLogout',
    data: function () {
        return {
            events: ['click', 'mousemove', 'mousedown', 'scroll', 'keypress', 'load'],
            warningTimer: null,
            logoutTimer: null,
            warningZone: false,
        }
    },
    mounted() {
        this.events.forEach(function (event) {
            window.addEventListener(event, this.resetTimer);
        }, this);
        this.setTimers();
    },
    unmounted() {
        this.events.forEach(function (event) {
            window.removeEventListener(event, this.resetTimer);
        }, this);
        clearTimeout(this.warningTimer);
        clearTimeout(this.logoutTimer);
    },
    methods: {
        setTimers: function() {
            console.log((Number(import.meta.env.VITE_SESSION_LIFETIME) || 120));
            this.warningTimer = setTimeout(this.warningMessage, Math.max(0, (Number(import.meta.env.VITE_SESSION_LIFETIME) || 120) - 1) * 60 * 1000); // 14 minutes - 14 * 60 * 1000
            this.logoutTimer = setTimeout(this.logoutUser, (Number(import.meta.env.VITE_SESSION_LIFETIME) || 120) * 60 * 1000); // 15 minutes - 15 * 60 * 1000
            this.warningZone = false;
        },
        warningMessage: function() {
            this.warningZone = true;
        },
        logoutUser: function() {
            // Laravel
            // document.getElementById('logout-form').submit();
            var remember_token = window.localStorage.getItem("remember_token");
            console.log(remember_token == "null");

            if(remember_token == "null" || remember_token=="undefined"){
                this.axios
                    .post(`/api/logout`)
                    .then(({ data }) => {
                    this.auth.logout(); //reset local storage
                    // this.$router.push("/login");
                    window.localStorage.removeItem("token");
                    window.localStorage.removeItem("remember_token");
                    window.localStorage.removeItem("user");
                    // this.user = null;
                    window.location.href = "/login";
                    })
                    .catch((error) => {
                    console.log(error);
                });
            }

        },
        resetTimer: function() {
            clearTimeout(this.warningTimer);
            clearTimeout(this.logoutTimer);
            this.setTimers();
        }
    }
}
</script>
