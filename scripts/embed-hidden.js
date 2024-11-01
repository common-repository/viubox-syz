// Code to validate URL
jQuery(document).ready(function ($) {
  // var origin = window.location.origin;
  $(window).bind('load', function () {
    axios
      .post('https://api.viubox.com:8000/portal/checkurl', { url: origin })
      .then((res) => {
        if (res.data.urlExists === true) {
          let html =
            '<iframe id="receiver" class="chatbox" allow="camera; microphone" allowtransparency=true style="height:100vh; position: fixed;z-index: 1310; right: -400px;overflow-x: hidden;top:0; border: 0px; width: 400px;" src = "https://syz.viubox.com"></iframe ><button id="iconbtn" style="display:none; cursor:pointer; border:none; overflow-x:hidden; background:none; position:fixed; right:-400px; top:46vh ; z-index:200;display:flex;align-items:center" ><p class="texthover" style="display:none; opacity:0;margin:0px 8px 0px 0px;line-height:20px;font-size:18px;font-weight:500;color:#3d3d3d;"><span style="display:none; padding-right:7px;font-Weight:500;opacity:0.5;">&lt;</span> Try <br/> Online</p><img src="https://widget.viubox.com/img/appicon.png" width="70px" draggable="false" alt="appicon" border="0" style="display:none;"></button>';
          // document.body.innerHTML += html;
          $('body').append(html);

          let overlay = "<div class='viubox-syz-overlay'></div>"
          $('body').append(overlay);
          document.querySelector('.viubox-syz-overlay').style.transition =
            'all 0.5s ease-in-out';
          document.querySelector('.viubox-syz-overlay').style.cursor =
            "url('https://widget.viubox.com/img/delete-cursor-32x32.png'), auto";
          // Main event loop

          $('#receiver').on('load', function () {
            const enableScroll = () => {
              document.querySelector('body').style.overflow = 'auto';
              $(".viubox-syz-overlay").css("visibility", "hidden")
              $(".viubox-syz-overlay").css("opacity", 0)
            };
            const disableScroll = () => {
              document.querySelector('body').style.overflow = 'hidden';
              $(".viubox-syz-overlay").css("visibility", "visible")
              $(".viubox-syz-overlay").css("opacity", 1)

            };
            // Redundant
            $(this).width('400px');
            $('#iconbtn').css('right', '10px');

            // Removing Transparent background for mobile screens
            if (window.innerWidth < 400) {
              $('#receiver').css('background-color', '#ffffff');
            }

            // This piece of code sends the postMessage when 'Check your fit' button is clicked with the sku
            var receiver = document.getElementById('receiver').contentWindow;
            receiver.postMessage(
              {
                message: 'origin',
                origin: origin,
              },
              '*'
            );
            // checking lang
            if (localStorage.getItem('lang')) {
              receiver.postMessage(
                {
                  message: 'langFromLocalStorage',
                  lang: localStorage.getItem('lang'),
                },
                '*'
              );
            }
            // Windows Parameters
            const screenRatio = (
              window.screen.availWidth / window.screen.availHeight
            ).toFixed(2);
            // const memory = navigator.deviceMemory ? navigator.deviceMemory : 32; //RAM Check
            const cookie = navigator.cookieEnabled ? 1 : 0;
            // Pixel depth
            // const pixel = window.screen.pixelDepth ? window.screen.pixelDepth : 0;
            const processors = navigator.hardwareConcurrency
              ? navigator.hardwareConcurrency
              : 16; // Number of processors
            const browserLength = navigator.userAgent
              ? navigator.userAgent.length
              : 50; // length of the browser string
            const touchSupport = navigator.maxTouchPoints
              ? navigator.maxTouchPoints
              : 0; // Touch support
            // Getting IP address of the user
            $.getJSON('https://api.ipify.org?format=json')
              .done(function (data) {
                // const IPAdr = `${data.ip}.${screenRatio}.${memory}.${browserLength}.${touchSupport}.${cookie}.${processors}`;
                const IPAdr = `${data.ip}.${screenRatio}.${browserLength}.${touchSupport}.${cookie}.${processors}`;
                console.log(`Unlimited Requests:${IPAdr}`);
                receiver.postMessage(
                  { message: 'userIp', publicIp: IPAdr },
                  '*'
                );
              })
              .fail(function () {
                const APIKey = 'ea67a6de99ce41339e2c7c60f68be0d7';
                const url = `https://api.ipgeolocation.io/ipgeo?apiKey=${APIKey}`;
                $.getJSON(url)
                  .done(function (data) {
                    // const IPAdr = `${data.ip}.${screenRatio}.${memory}.${browserLength}.${touchSupport}.${cookie}.${processors}`;
                    const IPAdr = `${data.ip}.${screenRatio}.${browserLength}.${touchSupport}.${cookie}.${processors}`;
                    console.log(`Limited Requests:${IPAdr}`);
                    receiver.postMessage(
                      { message: 'userIp', publicIp: IPAdr },
                      '*'
                    );
                  })
                  .fail(function () {
                    if (localStorage.getItem('authToken')) {
                      receiver.postMessage(
                        {
                          message: 'authFromParent',
                          authToken: localStorage.getItem('authToken'),
                        },
                        '*'
                      );
                    }
                  });
              });

            $('.viubox-syz-measurments-button').on('click', function () {
              // $('.viubox-syz-measurments-button').css('background-color', '#6f928a');
              $('#receiver').css('right', '0px');
              $('#receiver').css('transition', 'all 0.5s');
              disableScroll();
              // $('body').css('overflow-y', 'hidden');
              var sku = $(this).data('sku');
              var syzsku = $(this).data('syzsku');
              // const myiframe = document.getElementById('receiver');
              var message = {
                message: 'virtual-dress-view-open',
                productSku: syzsku,
              };
              receiver.postMessage(message, '*');
            });
            // Opening button
            $('#iconbtn').on('click', function () {
              $('#receiver').css('right', '0px');
              $('#receiver').css('transition', 'all 0.5s');
              disableScroll();
              // $('body').css('overflow-y', 'hidden');

              var sku = $(this).data('sku');
              var syzsku = $(this).data('syzsku');
              // const myiframe = document.getElementById('receiver');
              var message = {
                message: 'virtual-dress-view-open',
                productSku: syzsku,
              };
              receiver.postMessage(message, '*');
            });

            //hover effect
            $('#iconbtn').hover(
              function () {
                $('.texthover').css('opacity', '1');
                $('.texthover').css('transition', '0.4s ease');
              },
              function () {
                $('.texthover').css('opacity', '0');
                $('.texthover').css('transition', '0.4s ease');
              }
            );

            // if overlay is clicked
            $('.viubox-syz-overlay').on('click', function () {
              $('#receiver').css('right', '-400px');
              $('#receiver').css('transition', 'all 0.5s');
              enableScroll();
              // $('body').css('overflow-y', 'auto');
              var message = {
                message: 'virtual-dress-view-close',
              };
              receiver.postMessage(message, '*');
            });

            // This loop runs when the client window receives a message
            window.addEventListener('message', function (event) {
              event.preventDefault();

              // change the width based on state of the app
              if (event.data && event.data.message == 'Close App') {
                // $('#receiver').css('width', '0px');
                $('#receiver').css('right', '-400px');
                $('#receiver').css('height', '100vh');
                $('#receiver').css('transition', 'all 0.5s');
                enableScroll();
                // $('body').css('overflow-y', 'auto');
                // $('.viubox-syz-measurments-button').css('background-color', '#000000');
              } else if (
                event.data &&
                event.data.message == 'virtual-dress-view-open'
              ) {
                // $('#receiver').css('width', '400px');
                $('#receiver').css('right', '0px');
                $('#receiver').css('height', '100vh');
                $('#receiver').css('transition', 'all 0.5s');
                disableScroll();
                // $('body').css('overflow-y', 'hidden');
              } else if (event.data && event.data.message === 'addToken') {
                localStorage.setItem('authToken', event.data.authToken);
              } else if (event.data && event.data.message === 'removeToken') {
                localStorage.removeItem('authToken');
              } else if (
                event.data &&
                event.data.message === 'authFromParent'
              ) {
                localStorage.setItem('authToken', event.data.authFromParent);
              } else if (event.data && event.data.message === 'changeLang') {
                localStorage.setItem('lang', event.data.flag);
              }
              // else if (event.data && event.data.message === 'ipTokenExists') {
              //   if (localStorage.getItem('authToken')) {
              //     receiver.postMessage(
              //       {
              //         message: 'authFromParent',
              //         authToken: localStorage.getItem('authToken'),
              //       },
              //       '*'
              //     );
              //   }
              // }
            });
          });
        } else if (res.data.urlExists === false) {
          console.log(
            'Viubox is not enabled for this domain. Please contact an administrator.'
          );
        } else {
          console.log('ViuBox is having issues. Please try again later');
        }
      })
      .catch((err) => {
        console.log(err);
      })
      .then(() => {
        console.log('ViuBox SYZ successfully initialised');
      });
  });
});
