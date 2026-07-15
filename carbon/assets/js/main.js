/*=== Javascript function indexing hear===========

1.counterUp ----------(Its use for counting number)
2.stickyHeader -------(header class sticky)
3.wowActive ----------( Waw js plugins activation)
4.swiperJs -----------(All swiper in this website hear)
5.salActive ----------(Sal animation for card and all text)
6.textChanger --------(Text flip for banner section)
7.timeLine -----------(History Time line)
8.datePicker ---------(On click date calender)
9.timePicker ---------(On click time picker)
10.timeLineStory -----(History page time line)
11.vedioActivation----(Vedio activation)
12.searchOption ------(search open)
13.cartBarshow -------(Cart sode bar)
14.sideMenu ----------(Open side menu for desktop)
15.Back to top -------(back to top)
16.filterPrice -------(Price filtering)

==================================================*/

(function ($) {
  'use strict';
  let device_width = window.innerWidth;

  var invJs = {
    m: function (e) {
      invJs.d();
      invJs.methods();
    },
    d: function (e) {
      this._window = $(window),
        this._document = $(document),
        this._body = $('body'),
        this._html = $('html')
    },
    methods: function (e) {
      invJs.stickyElements();
      invJs.textAnimation();
      invJs.gsapSticky();
      invJs.textHoverGsap();
      invJs.cartDelete();
      invJs.odoMeter();
      invJs.stickyHeader();
      // invJs.wowActive();
      invJs.swiperJs();
      invJs.salActive();
      invJs.videoActivation();
      invJs.searchOption();
      invJs.cartBarshow();
      invJs.sideBarTwoshow();
      invJs.metismenu();
      invJs.sideMenu();
      invJs.backToTopInit();
      invJs.filterPrice();
      invJs.onePageNav();
      invJs.serviceHover();
      invJs.countDown();
      invJs.magnificPopupActivation();
      invJs.categoryMenuHover();
      invJs.filterActionButton();
      invJs.showMoreBtn();
      invJs.menuCurrentLink();
      invJs.addtoWishlist();
      invJs.dateUpdate();
      invJs.uploadImage();
      invJs.customSelect();
      invJs.contactForm();
      invJs.checkoutPage();
      invJs.dataCss();
    },




    stickyElements: function(){
      let mediaMatch = gsap.matchMedia();

      function initStickyPanel3Animation() {
        const container = document.querySelector(".inversweb-sticky-panel-1-container");
        const panels = document.querySelectorAll(".inversweb-sticky-panel-1");
        if (!container || panels.length === 0) return;
        mediaMatch.add("(min-width: 992px)", () => {
          const startOffset =
            parseInt(getComputedStyle(container).paddingTop, 10) || 120;  // â† radix 10
          const lastIdx = panels.length - 1;
          const lastPanel = panels[lastIdx];
          const paddingBottom =
            parseInt(getComputedStyle(container).paddingBottom, 10) || 0;  // â† radix 10
          panels.forEach((panel, i) => {
            gsap.to(panel, {
              scrollTrigger: {
                trigger: panel,
                start: `top-=${startOffset} top`,
                endTrigger: container,
                end: () =>
                  `bottom top+=${
                    lastPanel.offsetHeight + startOffset + paddingBottom
                  }`,
                pin: true,
                pinSpacing: false,
                scrub: true,
                markers: false,
                invalidateOnRefresh: true,
              },
              ease: "circ",
              opacity: i === 0 || i === lastIdx ? 1 : 0.1,
            });
          });
        });
      }
      initStickyPanel3Animation();
    },


    textAnimation: function () {
      const preloader = document.getElementById("Invers-sitePreloader");

      window.addEventListener("load", function () {
        document.body.classList.add("loaded");

        setTimeout(function () {
          if (preloader) {
            preloader.classList.add("is-hidden");
          }

          setTimeout(function () {
            initTextAnimations();

            if (typeof ScrollTrigger !== "undefined") {
              ScrollTrigger.refresh();
            }
          },);
        },);
      });

      function initTextAnimations() {
        let mediaMatch = gsap.matchMedia();

        function isRTL() {
          return document.documentElement.dir === "rtl";
        }

        function rtlValue(value) {
          return isRTL() ? -value : value;
        }

        /* Text Effect Animation */
        if ($(".inv-text-anim").length) {
          let staggerAmount = 0.02,
            translateXValue = rtlValue(20),
            delayValue = 0.1,
            easeType = "power2.out",
            animatedTextElements = document.querySelectorAll(".inv-text-anim");

          animatedTextElements.forEach((element) => {
            const rtl = isRTL();

            let animationSplitText = new SplitText(element, {
              type: rtl ? "words" : "chars, words",
            });

            gsap.from(rtl ? animationSplitText.words : animationSplitText.chars, {
              duration: 1,
              delay: delayValue,
              x: translateXValue,
              autoAlpha: 0,
              stagger: staggerAmount,
              ease: easeType,
              scrollTrigger: {
                trigger: element,
                start: "top 85%",
              },
            });
          });
        }

        if ($(".inv-text-anim-y").length) {
          let staggerAmount = 0.01,
            delayValue = 0.1,
            easeType = "power1.inout",
            animatedTitleElements = document.querySelectorAll(".inv-text-anim-y");

          animatedTitleElements.forEach((element) => {
            let splitTitle = new SplitText(element, {
              type: "words, chars",
            });

            gsap.from(splitTitle.chars, {
              y: "100%",
              duration: 0.5,
              delay: delayValue,
              autoAlpha: 0,
              stagger: staggerAmount,
              ease: easeType,
              scrollTrigger: {
                trigger: element,
                start: "top 85%",
              },
            });
          });
        }

        if ($(".inw-text-shaking").length) {
          var waSplitup = $(".inw-text-shaking");

          gsap.registerPlugin(SplitText);

          waSplitup.each(function (index, el) {
            el.split = new SplitText(el, {
              type: "lines,words,chars",
              linesClass: "split-line",
            });

            let delayValue = $(el).attr("data-split-delay") || "0s";
            delayValue = parseFloat(delayValue) || 0;

            gsap.set(el.split.chars, {
              rotate: 90,
              opacity: 0,
            });

            el.anim = gsap.to(el.split.chars, {
              scrollTrigger: {
                trigger: el,
                start: "top 86%",
                toggleActions: "play none none reverse",
              },
              opacity: 1,
              rotate: 0,
              duration: 0.8,
              ease: "back.out(3)",
              stagger: 0.08,
              delay: delayValue,
            });
          });
        }
      }

    },


    gsapSticky: function () {
      let mediaMatch = gsap.matchMedia();
        $(document).ready(function () {
        const serviceStack = gsap.utils.toArray(".gsap-sticky");
        if (serviceStack.length > 0) {
          mediaMatch.add("(min-width: 992px)", () => {
            serviceStack.forEach(item => {
              gsap.to(item, {
                opacity: 0,
                scale: 0.9,
                y: 30,
                scrollTrigger: {
                  trigger: item,
                  scrub: true,
                  start: "top top",
                  pin: true,
                  pinSpacing: false,
                  markers: false,
                },
              });
            });
          });
        }

      });
      $(document).ready(function () {
        const serviceStack = gsap.utils.toArray(".gsap-sticky-top-120");

        if (serviceStack.length > 0) {
          mediaMatch.add("(min-width: 992px)", () => {
            serviceStack.forEach(item => {
              gsap.to(item, {
                opacity: 0,
                scale: 0.9,
                y: 30,
                scrollTrigger: {
                  trigger: item,
                  scrub: true,
                  start: "top-=120 top",
                  pin: true,
                  pinSpacing: false,
                  markers: false,
                },
              });
            });
          });
        }
      });

    },


    textHoverGsap: function(){
      const headings = document.querySelectorAll('.text-scale-anim');

      headings.forEach(heading => {
        const textNodes = [];

        heading.childNodes.forEach(node => {
          if (node.nodeType === Node.TEXT_NODE) {
            node.textContent.split(' ').forEach((word, index, array) => {
              const wordSpan = document.createElement('span');
              wordSpan.classList.add('invers-word-span');

              word.split('').forEach(letter => {
                const letterSpan = document.createElement('span');
                letterSpan.classList.add('invers-letter-span');
                letterSpan.textContent = letter;
                wordSpan.appendChild(letterSpan);
              });

              textNodes.push(wordSpan);

              if (index < array.length - 1) {
                textNodes.push(document.createTextNode(' '));
              }
            });
          } else {
            textNodes.push(node.cloneNode(true));
          }
        });

        heading.innerHTML = '';
        textNodes.forEach(node => heading.appendChild(node));

        const letters = heading.querySelectorAll('.invers-letter-span');

        letters.forEach((letter, index) => {

          const prev = letters[index - 1];
          const next = letters[index + 1];

          letter.addEventListener('mouseenter', () => {
            gsap.to(letter, {
              scaleY: 1.6,
              y: '-24%',
              color: 'var(--color-primary)',
              duration: 0.4,
              ease: 'power2.out',
              overwrite: true
            });

            if (prev) {
              gsap.to(prev, {
                scaleY: 1.3,
                y: '-12%',
                color: 'var(--color-primary)',
                duration: 0.4,
                ease: 'power2.out',
                overwrite: true
              });
            }

            if (next) {
              gsap.to(next, {
                scaleY: 1.3,
                y: '-12%',
                color: 'var(--color-primary)',
                duration: 0.4,
                ease: 'power2.out',
                overwrite: true
              });
            }
          });

          letter.addEventListener('mouseleave', () => {
            gsap.to([letter, prev, next], {
              scaleY: 1,
              y: '0%',
              color: '',
              duration: 0.4,
              ease: 'power2.out',
              overwrite: true
            });
          });

        });
      });
    },

    cartDelete: function () {
      // All delete buttons select kora
      document.querySelectorAll('.delete-cart').forEach(function (btn) {
          btn.addEventListener('click', function (e) {
              e.preventDefault(); // default anchor action off korar jonne
              const productItem = this.closest('.product-item');
              if (productItem) {
                  productItem.remove(); // item remove hobe
              }
          });
      });

      // All delete buttons select kora
      document.querySelectorAll('.cart-table .remove-btn').forEach(function (btn) {
          btn.addEventListener('click', function (e) {
              e.preventDefault(); // default anchor action off korar jonne
              const productItem = this.closest('tr');
              if (productItem) {
                  productItem.remove(); // item remove hobe
              }
          });
      });

    },


    odoMeter: function () {
      window.addEventListener('load', function () {
        if (document.querySelector('.odometer')) {
          $(document).ready(function () {
            function isInViewport(element) {
              const rect = element.getBoundingClientRect();
              return (
                rect.top >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)
              );
            }

            function triggerOdometer(element) {
              const $element = $(element);
              if (!$element.hasClass('odometer-triggered')) {
                const countNumber = $element.attr('data-count');
                $element.html(countNumber);
                $element.addClass('odometer-triggered'); // Add a class to prevent re-triggering
              }
            }

            function handleOdometer() {
              $('.odometer').each(function () {
                if (isInViewport(this)) {
                  triggerOdometer(this);
                }
              });
            }

            // Check on page load
            handleOdometer();

            // Check on scroll
            $(window).on('scroll', function () {
              handleOdometer();
            });
          });

        }
      });
    },

    // sticky header activation
    stickyHeader: function (e) {

      $(window).scroll(function () {
        if ($(this).scrollTop() > 150) {
          $('.header--sticky').addClass('sticky')
        } else {
          $('.header--sticky').removeClass('sticky')
        }
      })

      $(document).ready(function () {
          var $header = $('.header--sticky.header-sticky-smooth');
          var initialHeight = $header.outerHeight(); // Default header height (with header-top)

          $(window).scroll(function () {
              if ($header.length) {
                  if ($(this).scrollTop() > 150) {
                      // Use always the initial full height
                      $('body').css('padding-top', initialHeight + 'px');
                  } else {
                      $('body').css('padding-top', '0');
                  }
              }
          });
      });


    },

    // waw js activation
    // wowActive: function () {
    //   new WOW().init();
    // },

    // All swiper js
    swiperJs: function () {

      $(document).ready(function () {
        var swiper = new Swiper(".mySwiper-case-4", {
          spaceBetween: 30,
          loop: "true",
          speed: 1000,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          breakpoints: {
            1199: {
              slidesPerView: 4,
              spaceBetween: 30,
            },
            992: {
              slidesPerView: 3,
              spaceBetween: 30,
            },
            767: {
              slidesPerView: 2,
              spaceBetween: 30,
            },
            539: {
              slidesPerView: 2,
              spaceBetween: 30,
            },
            0: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
          },

        });
      
      });


      $(document).ready(function () {

        var swiper = new Swiper(".mySwiper-team-large-2", {
          spaceBetween: 30,
          centeredSlides: true,
          slidesPerView: 3,
          loop: "true",
          speed: 1000,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          breakpoints: {
            992: {
              slidesPerView: 3,
              spaceBetween: 30
            },
            767: {
              slidesPerView: 2,
              spaceBetween: 30
            },
            0: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
          },

        });


      });

      $(document).ready(function () {

        var swiperThumb = new Swiper(".testimonial-three--slider-thumb", {
          spaceBetween: 20,
          speed: 1000,
          freeMode: true,
          breakpoints: {
            767: {
              slidesPerView: 3,
            },
            0: {
              slidesPerView: 3,
            },
          },
        });
        var swiper = new Swiper(".testimonial-three--slider", {
          spaceBetween: 30,
          speed: 1000,
          thumbs: {
            swiper: swiperThumb,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
        });


      });

      
      // testimonials fluid style***
      $(document).ready(function () {
        
          var swiper = new Swiper(".testimonial-slider-fluid", {
            loop: "true",
            spaceBetween: 50,
            speed: 1000,
            autoplay: {
              delay: 5000,
              disableOnInteraction: false,
            },
            navigation: {
              nextEl: ".swiper-button-next4",
              prevEl: ".swiper-button-prev4",
            },
            breakpoints: {
              767: {
                slidesPerView: 2,
              },
            },
          });


      });


      // Blog Two Item Show***
      $(document).ready(function () {
        
          var swiper = new Swiper(".blog-show-two-item-active", {
            loop: true,
            spaceBetween: 30,
            speed: 1000,
            autoplay: {
              delay: 5000,
              disableOnInteraction: false,
            },
            pagination: {
              el: ".swiper-pagination-blog-two",
              clickable: true,
            },
            breakpoints: {
              767: {
                slidesPerView: 2,
                spaceBetween: 30
              },
              0: {
                slidesPerView: 1,
                spaceBetween: 0,
              },
            },
          });


      });

      // Blog Two Item Show***
      $(document).ready(function () {
        
          var swiper = new Swiper(".blog-show-three-item-active", {
            loop: true,
            spaceBetween: 30,
            slidesPerView: 3,
            speed: 1000,
            autoplay: {
              delay: 3000,
              disableOnInteraction: false,
            },
            pagination: {
              el: ".swiper-pagination-blog-two",
              clickable: true,
            },
            breakpoints: {
              992: {
                slidesPerView: 3,
                spaceBetween: 30
              },
              767: {
                slidesPerView: 2,
                spaceBetween: 30
              },
              0: {
                slidesPerView: 1,
                spaceBetween: 0,
              },
            },
          });


      });


      // Swiper marqee area start here ***
      $(document).ready(function () {
        
        var marqueeSlider = new Swiper(".invers-marqueeSwiper--slider", {
          loop: true,
          freemode: true,
          slidesPerView: 1,
          spaceBetween: 0,
          centeredSlides: true,
          allowTouchMove: false,
          speed: 6000,
          autoplay: {
            delay: 1,
            disableOnInteraction: true,
          },
          breakpoints: {
            320: {
              slidesPerView: 2,
            },
            425: {
              slidesPerView: 3,
            },
            800: {
              slidesPerView: 4,
            },
            1024: {
              slidesPerView: 4,
            },
            1300: {
              slidesPerView: 5,
            },
            1900: {
              slidesPerView: 6,
            },
          },
        });


      });



      // project hover swiper
      $(document).ready(function () {

        // Project slider area start here ***
        var swiper = new Swiper(".project__slider", {
          spaceBetween: 0,
          speed: 1000,
          pagination: false,
          navigation: {
            nextEl: ".project__arry-next",
            prevEl: ".project__arry-prev",
          },
          mousewheel: false,
          keyboard: true,
          autoplay: false,
          loop: false,
          breakpoints: {
            0: {
              slidesPerView: 1,
            },
            480: {
              slidesPerView: 2,
            },
            787: {
              slidesPerView: 3,
            },
            991: {
              slidesPerView: 3,
            },
            1200: {
              slidesPerView: 4,
            },
          },
        });



        // Project change background image area end here ***
        $(".project__slider .swiper-slide").on("mouseenter click", function () {
          var tab_id = $(this).attr("data-tab");
          $(".project__slider .swiper-slide").removeClass("active");
          $(this).addClass("active");

          $(".project__image .tab-img ").removeClass("active");
          $("#" + tab_id).addClass("active");

          if ($(this).hasClass("active")) {
            return false;
          }
        });
        $(".project__arry-next").on("click", function () {
          // Perform the intended action here, e.g., trigger a slider move
          console.log("Next button clicked");
        });

        $(".project__arry-prev").on("click", function () {
          // Perform the intended action here, e.g., trigger a slider move
          console.log("Prev button clicked");
        });


      });



      // banner paralax-swiper

      (function BannerSlider() {
        var sliderActive1 = ".banner-slider-paralax";
        var sliderInit1 = new Swiper(sliderActive1, {
          loop: true,
          slidesPerView: 1,
          spaceBetween: 0,
          speed: 2000,
          parallax: true,
          autoplay: {
            delay: 8000,
            disableOnInteraction: false,
          },
          navigation: {
            nextEl: ".banner__arry-next",
            prevEl: ".banner__arry-prev",
          },
          on: {
            slideChange: function () {
              animateContent();
            },
          },
          pagination: {
            el: ".banner-dot",
            clickable: true,
            renderBullet: function (index, className) {
              const dotContent = document.querySelectorAll(
                ".banner-dot .dot-content"
              );
              return `
                    <span class="${className}">
                        ${dotContent[index]?.outerHTML || ""}
                    </span>
                `;
            },
          },
        });
        function animateContent() {
          var animatedElements = $("[data-animation]");
          animatedElements.each(function () {
            var $this = $(this);
            var anim = $this.data("animation");
            var delay = $this.data("delay") || "0s";
            var duration = $this.data("duration") || "1s";

            $this
              .removeClass(anim + " animated")
              .css({
                webkitAnimationDelay: delay,
                animationDelay: delay,
                webkitAnimationDuration: duration,
                animationDuration: duration,
              })
              .addClass(anim + " animated")
              .one("animationend", function () {
                $this.removeClass(anim + " animated");
              });
          });
        }
        animateContent();
      })();

      // banner paralax-swiper






      $(document).ready(function () {
        var swiper = new Swiper(".tmpSlider", {
          spaceBetween: 30,
          slidesPerView: 3,
          slidesPerGroup: 1,
          loop: true,
          speed: 1000,
          autoplay: false,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          breakpoints: {
            1500: {
              slidesPerView: 3,
            },
            1199: {
              slidesPerView: 3,
            },
            991: {
              slidesPerView: 2,
            },
            767: {
              slidesPerView: 2,
            },
            575: {
              slidesPerView: 1,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });

      $(document).ready(function () {
        var swiper = new Swiper(".tmpSlider-team-4", {
          spaceBetween: 30,
          slidesPerView: 4,
          slidesPerGroup: 1,
          loop: true,
          speed: 1000,
          autoplay: false,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          breakpoints: {
            1500: {
              slidesPerView: 4,
            },
            1199: {
              slidesPerView: 3,
            },
            991: {
              slidesPerView: 2,
            },
            767: {
              slidesPerView: 2,
            },
            575: {
              slidesPerView: 1,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });

      $(document).ready(function () {
        var swiper = new Swiper(".tmp-slider-active-project-2", {
          spaceBetween: 30,
          slidesPerView: 3,
          slidesPerGroup: 1,
          loop: true,
          speed: 1000,
          centeredSlides: true,
          autoplay: false,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            1500: {
              slidesPerView: 3,
            },
            1199: {
              slidesPerView: 3,
            },
            991: {
              slidesPerView: 2,
            },
            767: {
              slidesPerView: 2,
            },
            575: {
              slidesPerView: 1,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });

      $(document).ready(function () {
        var swiper = new Swiper(".tmpTestimonialSlider", {
          spaceBetween: 30,
          slidesPerView: 2,
          slidesPerGroup: 1,
          loop: true,
          speed: 1000,
          // autoplay: {
          //   delay: 4000,
          // },
          loopFillGroupWithBlank: true,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          breakpoints: {
            1500: {
              slidesPerView: 2,
            },
            1199: {
              slidesPerView: 2,
            },
            991: {
              slidesPerView: 2,
            },
            767: {
              slidesPerView: 1,
            },
            575: {
              slidesPerView: 1,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });
      $(document).ready(function () {
        var swiper = new Swiper(".tmpTestimonialSlider2", {
          spaceBetween: 30,
          slidesPerView: 2,
          slidesPerGroup: 1,
          loop: true,
          speed: 500,
          autoHeight: true,
          autoplay: {
            delay: 3000,
          },
          loopFillGroupWithBlank: true,
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          breakpoints: {
            1500: {
              slidesPerView: 2,
            },
            1199: {
              slidesPerView: 2,
            },
            991: {
              slidesPerView: 2,
            },
            767: {
              slidesPerView: 1,
              spaceBetween: 20,
            },
            575: {
              slidesPerView: 1,
              spaceBetween: 20,
            },
            0: {
              slidesPerView: 1,
              spaceBetween: 20,
            }
          },
        })
      });
      $(document).ready(function () {
        var swiper = new Swiper(".tmpTestimonialSlider3", {
          spaceBetween: 30,
          slidesPerView: 4,
          slidesPerGroup: 1,
          loop: true,
          speed: 1500,
          autoplay: {
            delay: 4000,
          },
          loopFillGroupWithBlank: true,
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            1500: {
              slidesPerView: 4,
            },
            1199: {
              slidesPerView: 2,
            },
            991: {
              slidesPerView: 2,
            },
            767: {
              slidesPerView: 1,
            },
            575: {
              slidesPerView: 1,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });
      $(document).ready(function () {
        var swiper = new Swiper(".tmpBrandSlider", {
          spaceBetween: 30,
          slidesPerView: 6,
          slidesPerGroup: 1,
          loop: true,
          speed: 1000,
          autoplay: false,
          loopFillGroupWithBlank: true,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            1500: {
              slidesPerView: 6,
            },
            1199: {
              slidesPerView: 5,
            },
            991: {
              slidesPerView: 4,
            },
            767: {
              slidesPerView: 3,
            },
            575: {
              slidesPerView: 3,
            },
            0: {
              slidesPerView: 2,
            }
          },
        })
      });
      $(document).ready(function () {
        var swiper = new Swiper(".tmpBrandSlider-al.style-box", {
          spaceBetween: 30,
          slidesPerView: 6,
          slidesPerGroup: 1,
          loop: true,
          speed: 1000,
          autoplay: false,
          loopFillGroupWithBlank: true,
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            1500: {
              slidesPerView: 6,
            },
            1199: {
              slidesPerView: 5,
            },
            991: {
              slidesPerView: 5,
            },
            767: {
              slidesPerView: 3,
            },
            575: {
              slidesPerView: 3,
            },
            0: {
              slidesPerView: 2,
            }
          },
        })
      });
      $(document).ready(function () {
        var swiper = new Swiper(".tmpTeamSlider", {
          spaceBetween: 30,
          slidesPerView: 4,
          slidesPerGroup: 1,
          loop: true,
          speed: 1000,
          autoplay: false,
          loopFillGroupWithBlank: true,
          navigation: {
            nextEl: ".swiper-button-next3",
            prevEl: ".swiper-button-prev3",
          },
          breakpoints: {
            1500: {
              slidesPerView: 4,
            },
            1199: {
              slidesPerView: 4,
            },
            992: {
              slidesPerView: 3,
            },
            767: {
              slidesPerView: 2,
            },
            575: {
              slidesPerView: 2,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });

      $(document).ready(function () {
        var swiper = new Swiper(".tmpWorkingSlider", {
          spaceBetween: 30,
          slidesPerView: 3,
          slidesPerGroup: 1,
          centeredSlides: true,
          loop: true,
          speed: 1000,
          autoplay: {
            delay: 4000,
          },
          loopFillGroupWithBlank: true,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            1500: {
              slidesPerView: 3,
            },
            1199: {
              slidesPerView: 2,
            },
            991: {
              slidesPerView: 2,
            },
            767: {
              slidesPerView: 2,
              centeredSlides: false,
            },
            575: {
              slidesPerView: 1,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });

      $(document).ready(function () {
        var swiper = new Swiper(".mySwiper-events-grid", {
          spaceBetween: 30,
          slidesPerView: 3,
          slidesPerGroup: 1,
          loop: true,
          speed: 1000,
          autoplay: {
            delay: 4000,
          },
          loopFillGroupWithBlank: true,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            1500: {
              slidesPerView: 3,
            },
            1199: {
              slidesPerView: 3,
            },
            991: {
              slidesPerView: 2,
            },
            767: {
              slidesPerView: 2,
            },
            575: {
              slidesPerView: 1,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });


      $(document).ready(function () {

        // Check if the sliders exist in the DOM
        if ($(".SlideThumb").length && $(".thumbBannerSlide").length) {
          // Initialize Thumbnail Slider
          var swiperThumb = new Swiper(".SlideThumb", {
            slidesPerView: 3,
            spaceBetween: 0,
            watchSlidesProgress: true,
            watchSlidesVisibility: true,
            slideToClickedSlide: true,
          });

          // Initialize Main Slider
          var swiperMain = new Swiper(".thumbBannerSlide", {
            slidesPerView: 1,
            spaceBetween: 0,
            speed: 1300,
            effect: 'fade',
            autoHeight: true,
            navigation: {
              nextEl: ".swiper-button-next",
              prevEl: ".swiper-button-prev",
            },
            pagination: {
              el: ".swiper-pagination",
              clickable: true,
            },
            thumbs: {
              swiper: swiperThumb,
            },
            on: {
              init: function () {
                // Remove last 2 pagination bullets after Swiper fully initialized
                setTimeout(() => {
                  const bullets = document.querySelectorAll(".swiper-pagination .swiper-pagination-bullet");
                  if (bullets.length > 2) {
                    bullets[bullets.length - 1].remove();
                    bullets[bullets.length - 2].remove();
                  }
                }, 100); // delay 
              },
            },
          });

          swiperMain.on("slideChange", function () {
            const currentIndex = swiperMain.realIndex;
            const paginationBullets = document.querySelectorAll(".swiper-pagination .swiper-pagination-bullet");
            if (paginationBullets.length) {
              paginationBullets.forEach((bullet, index) => {
                bullet.classList.toggle("swiper-pagination-bullet-active", index === currentIndex);
              });
            }
          });

          // Finally initialize swiper
          swiperMain.init();
        }



      });


      $(document).ready(function () {

        // Check if the sliders exist in the DOM
        if ($(".SlideThumb").length && $(".thumbBannerSlideFour").length) {
          // Initialize Thumbnail Slider
          var swiperThumb = new Swiper(".SlideThumb", {
            slidesPerView: 3,
            spaceBetween: 0,
            watchSlidesProgress: true,
            watchSlidesVisibility: true,
            slideToClickedSlide: true,
          });

          // Initialize Main Slider
          var swiperMain = new Swiper(".thumbBannerSlideFour", {
            slidesPerView: 1,
            spaceBetween: 0,
            speed: 1300,
            loop: true,
            effect: 'fade',
            navigation: {
              nextEl: ".swiper-button-next",
              prevEl: ".swiper-button-prev",
            },
            pagination: {
              el: ".swiper-pagination",
              clickable: true,
            },
            thumbs: {
              swiper: swiperThumb,
            },
            on: {
              init: function () {
                // Remove last 2 pagination bullets after Swiper fully initialized
                setTimeout(() => {
                  const bullets = document.querySelectorAll(".swiper-pagination .swiper-pagination-bullet");
                  if (bullets.length > 2) {
                    bullets[bullets.length - 1].remove();
                    bullets[bullets.length - 2].remove();
                  }
                }, 100); // delay 
              },
            },
          });

          swiperMain.on("slideChange", function () {
            const currentIndex = swiperMain.realIndex;
            const paginationBullets = document.querySelectorAll(".swiper-pagination .swiper-pagination-bullet");
            if (paginationBullets.length) {
              paginationBullets.forEach((bullet, index) => {
                bullet.classList.toggle("swiper-pagination-bullet-active", index === currentIndex);
              });
            }
          });

          // Finally initialize swiper
          swiperMain.init();
        }



      });

      $(document).ready(function () {

        // Check if the sliders exist in the DOM
        if ($(".SlideThumbTwo").length && $(".thumbBannerSlideTwo").length) {
          // Initialize Thumbnail Slider
          var swiperThumb = new Swiper(".SlideThumbTwo", {
            slidesPerView: 3,
            spaceBetween: 0,
            watchSlidesProgress: true,
            watchSlidesVisibility: true,
            slideToClickedSlide: true,
          });

          // Initialize Main Slider
          var swiperMain = new Swiper(".thumbBannerSlideTwo", {
            slidesPerView: 1,
            spaceBetween: 0,
            speed: 1300,
            loop: true,
            effect: 'fade',
            navigation: {
              nextEl: ".swiper-button-next",
              prevEl: ".swiper-button-prev",
            },
            pagination: {
              el: ".swiper-pagination",
              clickable: true,
            },
            thumbs: {
              swiper: swiperThumb,
            },
            on: {
              init: function () {
                // Remove last 2 pagination bullets after Swiper fully initialized
                setTimeout(() => {
                  const bullets = document.querySelectorAll(".swiper-pagination .swiper-pagination-bullet");
                  if (bullets.length > 2) {
                    bullets[bullets.length - 1].remove();
                    bullets[bullets.length - 2].remove();
                  }
                }, 100); // delay 
              },
            },
          });

          swiperMain.on("slideChange", function () {
            const currentIndex = swiperMain.realIndex;
            const paginationBullets = document.querySelectorAll(".swiper-pagination .swiper-pagination-bullet");
            if (paginationBullets.length) {
              paginationBullets.forEach((bullet, index) => {
                bullet.classList.toggle("swiper-pagination-bullet-active", index === currentIndex);
              });
            }
          });

          // Finally initialize swiper
          swiperMain.init();
        }



      });

      $(document).ready(function () {

        // Initialize Main Slider
        var swiperMain = new Swiper(".mySwiper-slider-classic", {
          slidesPerView: 1,
          spaceBetween: 0,
          speed: 1300,
          loop: true,
          effect: 'fade',
          autoplay: {
            delay: 4000,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
        });



      });

      $(document).ready(function () {

        // Initialize Main Slider
        var swiperMain = new Swiper(".mySwiper-bg-inner-swiper", {
          slidesPerView: 1,
          spaceBetween: 0,
          speed: 1300,
          loop: true,
          effect: 'fade',
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
        });



      });

      $(document).ready(function () {

          var swiperThumb = new Swiper(".tmpmySwiperThumb", {
              spaceBetween: 10,
              slidesPerView: 2,
              freeMode: true,
              watchSlidesProgress: true,
          });

          // Initialize Main Slider
          var swiperMain = new Swiper(".thumbBannerSlide-wind", {
            slidesPerView: 1,
            spaceBetween: 0,
            speed: 1300,
            loop: true,
            effect: 'fade',
            navigation: {
              nextEl: ".swiper-button-next",
              prevEl: ".swiper-button-prev",
            },
            pagination: {
              el: ".swiper-pagination",
              clickable: true,
            },
            thumbs: {
              swiper: swiperThumb,
            },
          });

  

      });


      $(document).ready(function () {
        var swiper = new Swiper(".tmpServiceSlider2", {
          spaceBetween: 30,
          slidesPerView: 1.9,
          slidesPerGroup: 1,
          centeredSlides: false,
          loop: true,
          speed: 1000,
          // autoplay: {
          //   delay: 2500,
          // },
          loopFillGroupWithBlank: true,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            1500: {
              slidesPerView: 1.9,
            },
            1199: {
              slidesPerView: 1.8,
            },
            991: {
              slidesPerView: 1.2,
            },
            767: {
              slidesPerView: 1,
            },
            575: {
              slidesPerView: 1,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });
      $(document).ready(function () {
        var swiper = new Swiper(".tmpServiceSlider4", {
          spaceBetween: 30,
          slidesPerView: 2.4,
          slidesPerGroup: 1,
          centeredSlides: false,
          loop: true,
          speed: 1000,
          autoplay: {
            delay: 2500,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            1500: {
              slidesPerView: 2.4,
            },
            1199: {
              slidesPerView: 1.7,
            },
            991: {
              slidesPerView: 1.7,
            },
            767: {
              slidesPerView: 1,
            },
            575: {
              slidesPerView: 1,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });
      $(document).ready(function () {
        var swiper = new Swiper(".tmpServiceSlider", {
          spaceBetween: 27,
          slidesPerView: 3,
          slidesPerGroup: 1,
          centeredSlides: false,
          loop: true,
          speed: 1000,
          // autoplay: {
          //   delay: 2500,
          //   disableOnInteraction: false,
          // },
          loopFillGroupWithBlank: true,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            1500: {
              slidesPerView: 3,
            },
            1199: {
              slidesPerView: 3,
            },
            991: {
              slidesPerView: 3,
            },
            767: {
              slidesPerView: 2,
              autoplay: {
                delay: 2500,
              },
            },
            575: {
              slidesPerView: 1,
              autoplay: {
                delay: 2500,
              },
            },
            0: {
              slidesPerView: 1,
              autoplay: {
                delay: 2500,
              },
            }
          },
        })
      });
      $(document).ready(function () {
        var swiper = new Swiper(".tmpServiceSlider3", {
          spaceBetween: 15,
          slidesPerView: 8,
          slidesPerGroup: 1,
          centeredSlides: false,
          loop: true,
          speed: 1000,
          autoplay: {
            delay: 2500,
          },
          loopFillGroupWithBlank: true,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            1500: {
              slidesPerView: 8,
            },
            1199: {
              slidesPerView: 5,
            },
            991: {
              slidesPerView: 4,
            },
            767: {
              slidesPerView: 3,
            },
            400: {
              slidesPerView: 2,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });
      $(document).ready(function () {
        var swiper = new Swiper(".tmpTrendingSlider", {
          spaceBetween: 30,
          slidesPerView: 4,
          slidesPerGroup: 1,
          centeredSlides: false,
          loop: true,
          speed: 1000,
          // autoplay: {
          //   delay: 2500,
          // },
          loopFillGroupWithBlank: true,
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            1500: {
              slidesPerView: 4,
            },
            1199: {
              slidesPerView: 3,
            },
            991: {
              slidesPerView: 2,
            },
            767: {
              slidesPerView: 2,
            },
            575: {
              slidesPerView: 2,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });
      $(document).ready(function () {
        var swiper = new Swiper(".tmpProductSlider", {
          spaceBetween: 24,
          slidesPerView: 6,
          slidesPerGroup: 1,
          centeredSlides: false,
          loop: true,
          speed: 1000,
          autoplay: {
            delay: 2500,
          },
          loopFillGroupWithBlank: true,
          pagination: {
            el: ".product-pagination",
            type: "progressbar",
          },
          breakpoints: {
            1500: {
              slidesPerView: 6,
            },
            1199: {
              slidesPerView: 5,
            },
            991: {
              slidesPerView: 4,
            },
            767: {
              slidesPerView: 3,
            },
            575: {
              slidesPerView: 2,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });
      var swiper = new Swiper(".thumbBannerSlide2", {
        slidesPerView: 1,
        spaceBetween: 0,
        speed: 300,
        effect: "fade",
        loop: true,
        breakpoints: {
          575: {
            slidesPerView: 1,
          },
          0: {
            slidesPerView: 1,
          }
        },
        navigation: {
          nextEl: ".slide-next",
          prevEl: ".slide-prev",
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
      });
      $(document).ready(function () {
        // Initialize the first Swiper
        var swiperThumb = new Swiper(".tmp-testimonialSlider3", {
          slidesPerView: 1,
          slidesPerGroup: 1,
          spaceBetween: 20,
          speed: 1800,
          loop: true,
          watchSlidesProgress: true, // Sync progress
          watchSlidesVisibility: true, // Update visibility
          slideToClickedSlide: true,
          pagination: {
            el: ".swiper-pagination-n",
            clickable: true,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
        });
      });
      $(document).ready(function () {
        var swiper = new Swiper(".tmpTestimonialSlider4", {
          spaceBetween: 0,
          slidesPerView: 1,
          slidesPerGroup: 1,
          centeredSlides: false,
          loop: true,
          speed: 1000,
          autoHeight: true,
          autoplay: {
            delay: 2500,
          },
          loopFillGroupWithBlank: true,
          navigation: {
            nextEl: ".swiper-button-next2",
            prevEl: ".swiper-button-prev2",
          },
        })
      });
      $(document).ready(function () {
        var swiper = new Swiper(".projectSlider4", {
          spaceBetween: 0,
          slidesPerView: 1,
          slidesPerGroup: 1,
          centeredSlides: false,
          loop: true,
          speed: 1000,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          loopFillGroupWithBlank: true,
        })
      });
      $(document).ready(function () {
        var swiper = new Swiper(".tmpTeamSlider2", {
          spaceBetween: 30,
          slidesPerView: 4,
          slidesPerGroup: 1,
          loop: true,
          speed: 1500,
          // autoplay: {
          //   delay: 3000,
          // },
          loopFillGroupWithBlank: true,
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            1500: {
              slidesPerView: 4,
            },
            1199: {
              slidesPerView: 3,
            },
            991: {
              slidesPerView: 3,
            },
            767: {
              slidesPerView: 2,
            },
            575: {
              slidesPerView: 2,
            },
            0: {
              slidesPerView: 1,
            }
          },
        })
      });


      $(document).ready(function () {
        var swiper = new Swiper(".large-gallery-slider", {
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          autoplay: false,
          speed: 1500,
          loop: true,
        });
      });


      $(document).ready(function () {
        var swiper = new Swiper(".mySwiper-banner-shop", {

          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          pagination: {
            el: ".swiper-pagination-banner-shop",
            clickable: true,
          },
          // autoplay: {
          //   delay: 5500,
          // },
          loop: true,
          effect: 'fade',

        });
      });

      $(document).ready(function () {
          //Modal Gallery
          var swiper = new Swiper(".modal-gallery", {
            spaceBetween: 10,
            slidesPerView: 5,
            freeMode: true,
            watchSlidesProgress: true,
          });
          var swiper2 = new Swiper(".modal-gallery-big", {
            spaceBetween: 10,
            navigation: {
              nextEl: ".swiper-button-next",
              prevEl: ".swiper-button-prev",
            },
            thumbs: {
              swiper: swiper,
            },
          });
      });



    },

    // sal animatioin activation
    salActive: function () {
      sal({
        threshold: 0.1,
        once: true,
      });
    },

    videoActivation: function () {
      $(document).ready(function () {
        $('.popup-youtube, .popup-video').magnificPopup({
          type: 'iframe',
        });
      });
    },

    // search popup
    searchOption: function () {
      $(document).on('click', '#close', function () {
        $(".search-input-area").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });
      $(document).on('click', '#anywhere-home', function () {
        $(".search-input-area").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });
    },
    
    // cart bar show
    cartBarshow: function () {
      // Cart Bar show & hide
      $(document).on('click', '.cart-icon', function () {
        $(".cart-bar").addClass("show");
        $("#anywhere-home").addClass("bgshow");
      });
      $(document).on('click', '.close-cart', function () {
        $(".cart-bar").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });
      $(document).on('click', '#anywhere-home', function () {
        $(".cart-bar").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });


    },

    // cart bar show
    sideBarTwoshow: function () {
      // Cart Bar show & hide
      $(document).on('click', '.dot-btn', function () {
        $(".side-bar2").addClass("show");
        $("#anywhere-home").addClass("bgshow");
      });
      $(document).on('click', '.close-icon-menu', function () {
        $(".side-bar2").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });
      $(document).on('click', '#anywhere-home', function () {
        $(".side-bar2").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });



      $(function () {
        $(".button").on("click", function () {
          var $button = $(this);
          var $parent = $button.parent();
          var oldValue = $parent.find('.input').val();

          if ($button.text() == "+") {
            var newVal = parseFloat(oldValue) + 1;
          } else {
            // Don't allow decrementing below zero
            if (oldValue > 1) {
              var newVal = parseFloat(oldValue) - 1;
            } else {
              newVal = 1;
            }
          }
          $parent.find('a.add-to-cart').attr('data-quantity', newVal);
          $parent.find('.input').val(newVal);
        });
      });

    },

    // metismenu
    metismenu: function () {
      $('#mobile-menu-active').metisMenu();
    },

    // side menu desktop
    sideMenu: function () {
      $(document).on('click', '#menu-btn', function () {
        $("#side-bar").addClass("show");
        $("#anywhere-home").addClass("bgshow");
      });
      $(document).on('click', '.close-icon-menu', function () {
        $("#side-bar").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });
      $(document).on('click', '#anywhere-home', function () {
        $("#side-bar").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });
      $(document).on('click', '.onepage .mainmenu li a', function () {
        $("#side-bar").removeClass("show");
        $("#anywhere-home").removeClass("bgshow");
      });
    },

    slideNav: function () {
      window.slide = new SlideNav();
    },

    onePageNav: function (e) {
      $(document).ready(function () {
        var nav = $('#nav');
        if (nav.length) {
          $('#nav').onePageNav();
        }
      });
    },

    backToTopInit: function () {
            $(document).ready(function () {
        "use strict";

        var progressPath = document.querySelector('.progress-wrap path');
        var pathLength = progressPath.getTotalLength();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
        progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
        progressPath.style.strokeDashoffset = pathLength;
        progressPath.getBoundingClientRect();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
        var updateProgress = function () {
          var scroll = $(window).scrollTop();
          var height = $(document).height() - $(window).height();
          var progress = pathLength - (scroll * pathLength / height);
          progressPath.style.strokeDashoffset = progress;
        }
        updateProgress();
        $(window).scroll(updateProgress);
        var offset = 50;
        var duration = 550;
        jQuery(window).on('scroll', function () {
          if (jQuery(this).scrollTop() > offset) {
            jQuery('.progress-wrap').addClass('active-progress');
          } else {
            jQuery('.progress-wrap').removeClass('active-progress');
          }
        });
        jQuery('.progress-wrap').on('click', function (event) {
          event.preventDefault();
          jQuery('html, body').animate({ scrollTop: 0 }, duration);
          return false;
        })


      });

    },

    filterPrice: function () {
      var filter_price = $('.filter-price');
      if (filter_price.length) {
        var lowerSlider = document.querySelector('#lower');
        var upperSlider = document.querySelector('#upper');

        document.querySelector('#two').value = upperSlider.value;
        document.querySelector('#one').value = lowerSlider.value;

        var lowerVal = parseInt(lowerSlider.value);
        var upperVal = parseInt(upperSlider.value);

        upperSlider.oninput = function () {
          lowerVal = parseInt(lowerSlider.value);
          upperVal = parseInt(upperSlider.value);

          if (upperVal < lowerVal + 4) {
            lowerSlider.value = upperVal - 4;
            if (lowerVal == lowerSlider.min) {
              upperSlider.value = 4;
            }
          }
          document.querySelector('#two').value = this.value
        };

        lowerSlider.oninput = function () {
          lowerVal = parseInt(lowerSlider.value);
          upperVal = parseInt(upperSlider.value);
          if (lowerVal > upperVal - 4) {
            upperSlider.value = lowerVal + 4;
            if (upperVal == upperSlider.max) {
              lowerSlider.value = parseInt(upperSlider.max) - 4;
            }
          }
          document.querySelector('#one').value = this.value
        };
      }
    },



    serviceHover: function () {
      $('.single-varticle-product').hover(function () {
        $('.single-varticle-product.active').removeClass('active');
        $(this).addClass('active');
      });
    },

    countDown: function () {
      const countDown = {
        endDate: [],
        validElements: [],
        display: [],
        initialHeight: undefined,
        initialInnerDivMarginTop: undefined,
        originalBorderTopStyle: undefined,

        init: function () {
          $('.countDown').each(function () {
            const regex_match = $(this).text().match(/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/);
            if (!regex_match) return;

            const end = new Date(regex_match[3], regex_match[2] - 1, regex_match[1], regex_match[4], regex_match[5], regex_match[6]);

            if (end > new Date()) {
              countDown.validElements.push($(this));
              countDown.endDate.push(end);
              countDown.changeTime($(this), end);
              $(this).html(countDown.display.next.map(item => `<div class='container'><div class='a'><div>${item}</div></div></div>`).join(''));
            } else {
              $(this).html("<p class='end'>Sorry, your session has expired.</p>");
            }
          });
        },

        reset: function (element) {
          // Optionally implement any reset animation or class cleanup here
        },

        changeTime: function (element, endTime) {
          if (!endTime) return;

          const now = new Date();
          if (now <= endTime) {
            this.display = {
              last: this.calcTime(endTime.getTime() - now.getTime() + 1000),
              next: this.calcTime(endTime.getTime() - now.getTime())
            };
            this.display.next = this.display.next.map(item => item.toString().padStart(2, '0'));
            this.display.last = this.display.last.map(item => item.toString().padStart(2, '0'));

            element.find('div.container div.a div').each((index, div) => {
              $(div).text(this.display.last[index]);
            });

            this.reset(element.find('div.container'));
          } else {
            element.html("<p class='end'>Sorry, your session has expired.</p>");
          }
        },

        calcTime: function (milliseconds) {
          const secondsTotal = Math.floor(milliseconds / 1000);
          const days = Math.floor(secondsTotal / 86400);
          const hours = Math.floor((secondsTotal % 86400) / 3600);
          const minutes = Math.floor((secondsTotal % 3600) / 60);
          const seconds = secondsTotal % 60;
          return [days, hours, minutes, seconds];
        }
      };

      $(function () {
        countDown.init();

        function updateCountdowns() {
          countDown.validElements.forEach((element, i) => {
            countDown.changeTime(element, countDown.endDate[i]);
          });
        }
        updateCountdowns();
        setInterval(updateCountdowns, 2000);
      });
    },

    magnificPopupActivation: function () {
      $(".gallery-main-wrapper").magnificPopup({
        delegate: ".gallery-single",
        type: "image",
        mainClass: "mfp-with-zoom",
        gallery: {
          enabled: true,
        },
        zoom: {
          enabled: true, 
          duration: 300,
          easing: "ease-in-out",
          opener: function (openerElement) {
            return openerElement.is("img")
              ? openerElement
              : openerElement.find("img");
          },
        },
      });
    },

    categoryMenuHover: function () {
      $(".vertical-nav-menu li.vertical-nav-item").mouseover(function () {
        $(".tmp-vertical-inner").hide();
        $(".vertical-nav-menu li.vertical-nav-item").removeClass("active");
        $(this).addClass("active");
        var selected_tab = $(this).find("a").attr("href");
        $(selected_tab).stop().fadeIn();
        return false;
      });



    },


    filterActionButton: function () {
      $(".discover-filter-activation").on("click", function () {
        $(this).toggleClass("open");
        $(".default-exp-expand").slideToggle("400");
      });
      $("#slider-range").slider({
        range: true,
        min: 10,
        max: 500,
        values: [100, 300],
        slide: function (event, ui) {
          $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
        },
      });
      $("#amount").val(
        "$" +
          $("#slider-range").slider("values", 0) +
          " - $" +
          $("#slider-range").slider("values", 1)
      );
    },

    showMoreBtn: function () {
      $.fn.hasShowMore = function () {
        return this.each(function () {
          $(this).toggleClass("active");
          $(this).text("Show Less");
          $(this).parent(".has-show-more").toggleClass("active");
          if ($(this).parent(".has-show-more").hasClass("active")) {
            $(this).text("Show Less");
          } else {
            $(this).text("Show More");
          }
        });
      };
      $(document).on("click", ".tmp-show-more-btn", function () {
        $(this).hasShowMore();
      });
    },


    menuCurrentLink: function () {
        var currentPage = location.pathname.split("/"),
        current = currentPage[currentPage.length-1];
        $('.mainmenu li.has-menu-child-item a, .dashboard-mainmenu li a').each(function(){
            var $this = $(this);
            if($this.attr('href') === current){
                $this.addClass('active');
                $this.parents('.has-menu-child-item').addClass('menu-item-open')
            }
        });
    },

    addtoWishlist: function(){
     // Success color
  const successColor = '#28a745';
  const defaultColor = '#555';

  // Reusable toggle function
  function toggleAction(element, defaultIcon, activeIcon, defaultTooltip, activeTooltip) {
    const icon = element.querySelector('i');
    const isActive = icon.dataset.active === 'true';

    if (!isActive) {
      icon.className = `fa-solid ${activeIcon}`;
      icon.style.color = successColor;
      icon.dataset.active = 'true';
      element.setAttribute('data-tooltip', activeTooltip);
    } else {
      icon.className = defaultIcon;
      icon.style.color = defaultColor;
      icon.dataset.active = 'false';
      element.setAttribute('data-tooltip', defaultTooltip);
    }
  }

  // Wishlist Buttons (all)
  const wishlistBtns = document.querySelectorAll('.add-to-wishlist-action');
  wishlistBtns.forEach((btn) => {
    btn.addEventListener('click', function () {
      toggleAction(
        this,
        'fa-regular fa-heart',
        'fa-check',
        'Add to wishlist',
        'Added successfully'
      );
    });
  });

  // Compare Buttons (all)
  const compareBtns = document.querySelectorAll('.add-to-compare-action');
  compareBtns.forEach((btn) => {
    btn.addEventListener('click', function () {
      toggleAction(
        this,
        'fa-light fa-code-compare',
        'fa-check',
        'Add to Compare',
        'Added to compare'
      );
    });
  });
    },


    dateUpdate: function () {

      let fullYear = document.querySelectorAll("#year");

      if (fullYear.length) {
        window.addEventListener("DOMContentLoaded", function () {
          document.getElementById("year").textContent = new Date().getFullYear();
        });
      }

    },

    uploadImage: function () {

      let uploadImage = document.getElementsByClassName('invers-uploadBtnActive');

      if (uploadImage.length) {
        const uploadBtn = document.getElementById('uploadBtn');
        const fileInput = document.getElementById('fileInput');
        const previewImage = document.getElementById('previewImage');
        const authorImg = document.getElementById('authorImg'); // profile image

        uploadBtn.addEventListener('click', () => {
          fileInput.click(); // open file picker
        });

        fileInput.addEventListener('change', () => {
          const file = fileInput.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
              // replace both images
              previewImage.src = e.target.result;
              authorImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
          }
        });
      }


    },

    customSelect: function(){
      let index = 1;
        const on = (listener, query, fn) => {
          document.querySelectorAll(query).forEach(item => {
            item.addEventListener(listener, el => {
              fn(el);
            })
          })
        }

        on('click', '.selectBtn', item => {
          const next = item.target.nextElementSibling;
          next.classList.toggle('toggle');
          next.style.zIndex = index++;
        });
        on('click', '.option', item => {
          item.target.parentElement.classList.remove('toggle');

          const parent = item.target.closest('.select').children[0];
          parent.setAttribute('data-type', item.target.getAttribute('data-type'));
          parent.innerText = item.target.innerText;
        })
    },

    contactForm: function () {
      $('.tmp-dynamic-form').on('submit', function (e) {
        e.preventDefault();
        var _self = $(this);
        var __selector = _self.closest('input,textarea');
        _self.closest('div').find('input,textarea').removeAttr('style');
        _self.find('.error-msg').remove();
        _self.closest('div').find('button[type="submit"]').attr('disabled', 'disabled');
        var data = $(this).serialize();
        $.ajax({
          url: 'mail.php',
          type: "post",
          dataType: 'json',
          data: data,
          success: function (data) {
            _self.closest('div').find('button[type="submit"]').removeAttr('disabled');
            if (data.code == false) {
              _self.closest('div').find('[name="' + data.field + '"]');
              _self.find('.tmp-btn').after('<div class="error-msg"><p>*' + data.err + '</p></div>');
            } else {
              $('.error-msg').hide();
              $('.form-group').removeClass('focused');
              _self.find('.tmp-btn').after('<div class="success-msg"><p>' + data.success + '</p></div>');
              _self.closest('div').find('input,textarea').val('');

              setTimeout(function () {
                $('.success-msg').fadeOut('slow');
              }, 5000);
            }
          }
        });
      });
    },

    checkoutPage: function () {
      $(document).ready(function() {
        $(".single-payment-option").find("p.desc").hide();
        $(".single-payment-option:first").find("p.desc").show();

        // label 
        $(".single-payment-option .option").on("click", function() {
          $(".single-payment-option").find("p.desc").slideUp();

          $(this).closest(".single-payment-option").find("p.desc").slideDown();

          // checkbox handle 
          $(".single-payment-option input[type='checkbox']").prop("checked", false);
          $(this).find("input[type='checkbox']").prop("checked", true);
        });
      });

    },
    dataCss: function(){
      $("[data-background").each(function () {
        $(this).css("background-image", "url( " + $(this).attr("data-background") + "  )");
      });
    },


    

  }




  invJs.m();
})(jQuery, window)








;(function ($) {
    "use strict";

    function runVivusAnimation($wrapper) {
        var svg = $wrapper.find("svg")[0];

        if (!svg || typeof window.Vivus === "undefined") return;

        $wrapper.addClass("is-animating");

        new window.Vivus(svg, {
            duration: 110,
            type: "oneByOne",
            start: "autostart",
            onReady: function (vivus) {
                vivus.reset().play();
            }
        });
    }

    function svgVivusAnimation() {
        $(".corva-svg-animate").each(function () {
            var $wrapper = $(this);
            var svgInjectImages = $wrapper.find("img.svgInject");

            if (svgInjectImages.length && typeof window.SVGInject !== "undefined") {
                window.SVGInject(svgInjectImages.toArray(), {
                    makeIdsUnique: true
                });
            }
        });

        $(".corva-svg-animate")
            .on("mouseenter", function () {
                runVivusAnimation($(this));
            })
            .on("mouseleave", function () {
                $(this).removeClass("is-animating");
            });
    }

    $(document).ready(function () {
        svgVivusAnimation();
    });

})(jQuery);