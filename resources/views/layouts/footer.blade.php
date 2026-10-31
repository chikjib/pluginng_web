  <footer class="footer-3 page-section-pt">
      @php
          $settings = \App\Models\Setting::where('key', 'SOCIAL')->first();
          $setting = explode('|', $settings->value);
          $facebook = $setting[0];
          $instagram = $setting[1];
          $twitter = $setting[2];
          
      @endphp
      <div class="footer-widget">
          <div class="container">
              <script type="text/javascript"> //<![CDATA[
  var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/");
  document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
//]]></script>
<script language="JavaScript" type="text/javascript">
  TrustLogo("https://www.positivessl.com/images/seals/positivessl_trust_seal_md_167x42.png", "POSDV", "none");
</script>
<!-- End POSITIVE SSL -->
              <div class="row">
                  <div class="col-lg-8 col-md-8">
                      <p class="text-white mt-15"> &copy;Copyright <span id="copyright">
                              <script>
                                  document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear()))
                              </script>
                          </span> <a href="#"> {{ config('app.siteName') }}</a> All Rights Reserved

                          <a href="/terms">Terms & Conditions</a> | <a href="/privacy">Privacy Policy</a> | <a
                              href="https://documenter.getpostman.com/view/32987010/2sAYJAeHjy">API Documentation</a>
                              <!--https://documenter.getpostman.com/view/34324781/2sA3BkcYii-->
                      </p>


                  </div>
                  <div class="col-lg-4 col-md-4">
                      <div class="footer-widget-social">
                          <ul>
                              <li><a href="https://facebook.com/{{ $facebook }}" data-tooltip="facebook"><i
                                          class="fa fa-facebook"></i></a>
                              </li>
                              <li><a href="https://twitter.com/{{ $twitter }}" data-tooltip="twitter"><i
                                          class="fa fa-twitter"></i></a>
                              </li>
                              <li><a href="https://instagram.com/{{ $instagram }}" data-tooltip="instagram"><i
                                          class="fa fa-instagram"></i></a>
                              </li>

                          </ul>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </footer>
