(() => {
  // assets/js/hfg/utils.js
  var STX_FOCUS_TRAP_START = "stx-focus-trap-start";
  var STX_FOCUS_TRAP_END = "stx-focus-trap-end";
  var addEvent = (element, event, callBack) => {
    const array = element instanceof NodeList ? element : [element];
    for (let i = 0; i < array.length; i++) {
      array[i] && array[i].addEventListener(event, callBack);
    }
  };
  var toggleClass = (element, className) => {
    batchProcess(element, className, "toggle");
  };
  var addClass = (element, className) => {
    batchProcess(element, className, "add");
  };
  var removeClass = (element, className) => {
    batchProcess(element, className, "remove");
  };
  var batchProcess = (element, classNames, method) => {
    const classes = classNames.split(" ");
    const array = element instanceof NodeList ? element : [element];
    for (let i = 0; i < array.length; i++) {
      array[i] && array[i].classList[method].apply(array[i].classList, classes);
    }
  };

  // assets/js/hfg/navigation.js
  var strings = ["dropdown-open", "active", "nav-clickaway-overlay"];
  var initNavigation = () => {
    repositionDropdowns();
    handleScrollLinks();
    handleMobileDropdowns();
    handleSearch();
    handleMiniCartPosition();
    window.HFG.initSearch = function() {
      handleSearch();
      handleMobileDropdowns();
    };
  };
  var repositionDropdowns = () => {
    const dropDowns = document.querySelectorAll(".sub-menu, .minimal .stx-nav-search") || [];
    if (dropDowns.length === 0) return;
    const windowWidth = window.innerWidth;
    dropDowns.forEach((dropDown) => {
      const bounding = dropDown.getBoundingClientRect(), rightDist = bounding.left;
      if (rightDist < 0) {
        dropDown.style.right = "auto";
        dropDown.style.left = 0;
      }
      if (rightDist + bounding.width >= windowWidth) {
        dropDown.style.right = "100%";
        dropDown.style.left = "auto";
      }
    });
    if (typeof menuCalcEvent !== "undefined") {
      window.dispatchEvent(menuCalcEvent);
    }
  };
  function handleScrollLinks() {
    document.addEventListener("click", function(event) {
      if (event.target.hash && event.target.hash.includes("#")) {
        window.HFG.toggleMenuSidebar(false);
      }
    });
  }
  function handleMobileDropdowns() {
    const carets = document.querySelectorAll(".caret-wrap");
    addEvent(carets, "click", openCarrets);
    addEvent(carets, "keyup", openCarrets);
  }
  function openCarrets(e) {
    e.preventDefault();
    e.stopPropagation();
    if (e.keyCode && ![9, 13, 32].includes(e.keyCode)) {
      return;
    }
    const caret = e.currentTarget;
    const subMenu = caret.parentNode.parentNode.querySelector(".sub-menu");
    toggleClass(caret, strings[0]);
    caret.setAttribute(
      "aria-expanded",
      caret.classList.contains(strings[0]) ? "true" : "false"
    );
    toggleClass(subMenu, strings[0]);
    createNavOverlay(document.querySelectorAll(`.${strings[0]}`), strings[0]);
  }
  function getKeyboardFocusableElements(element = document) {
    focusTrapDetails.elements = [
      ...element.querySelectorAll(
        'a[href], button, input, textarea, select, details,[tabindex]:not([tabindex="-1"])'
      )
    ].filter(
      (el) => !el.hasAttribute("disabled") && !el.getAttribute("aria-hidden")
    );
    return focusTrapDetails.elements;
  }
  var focusTrapDetails = {};
  document.addEventListener(STX_FOCUS_TRAP_START, function(event) {
    focusTrapDetails = event.detail;
    setTimeout(() => {
      focusTrapDetails.container.querySelector(focusTrapDetails.firstFocus).focus();
    }, 100);
    document.addEventListener("keydown", startFocusTrap);
  });
  document.addEventListener(STX_FOCUS_TRAP_END, function() {
    focusTrapDetails = {};
    document.removeEventListener("keydown", startFocusTrap);
  });
  function startFocusTrap(event) {
    const elements = focusTrapDetails.elements || getKeyboardFocusableElements(focusTrapDetails.container);
    const tabKey = event.keyCode === 9;
    const shiftKey = event.shiftKey;
    const escKey = event.keyCode === 27;
    const activeEl = document.activeElement;
    const lastEl = elements[elements.length - 1];
    const firstEl = elements[0];
    if (escKey) {
      event.preventDefault();
      focusTrapDetails.container.querySelector(focusTrapDetails.close).click();
      focusTrapDetails.backFocus.focus();
      document.dispatchEvent(new CustomEvent(STX_FOCUS_TRAP_END));
    }
    if (!shiftKey && tabKey && lastEl === activeEl) {
      event.preventDefault();
      firstEl.focus();
    }
    if (shiftKey && tabKey && firstEl === activeEl) {
      event.preventDefault();
      lastEl.focus();
    }
    if (tabKey && firstEl === lastEl) {
      event.preventDefault();
    }
  }
  function handleSearch() {
    const navSearch = document.querySelectorAll(".stx-nav-search") || [], navItem = document.querySelectorAll(".menu-item-nav-search") || [], close = document.querySelectorAll(".close-responsive-search") || [];
    addEvent(navItem, "click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      toggleClass(e.currentTarget, strings[1]);
      createNavOverlay(e.currentTarget, strings[1]);
      document.dispatchEvent(
        new CustomEvent(STX_FOCUS_TRAP_START, {
          detail: {
            container: e.currentTarget.querySelector(".stx-nav-search"),
            close: ".close-responsive-search",
            firstFocus: ".search-field",
            backFocus: e.currentTarget
          }
        })
      );
    });
    addEvent(navSearch, "click", (e) => {
      e.stopPropagation();
    });
    addEvent(close, "click", (e) => {
      e.preventDefault();
      removeClass(navItem, strings[1]);
      const overlay = document.querySelector(`.${strings[2]}`);
      if (overlay === null) {
        return;
      }
      overlay.parentNode.removeChild(overlay);
    });
  }
  function handleMiniCartPosition() {
    const item = document.querySelector(".header--row .menu-item-nav-cart");
    if (item === null) {
      return;
    }
    const miniCart = item.querySelector(".stx-nav-cart");
    if (miniCart !== null) {
      miniCart.style.left = item.getBoundingClientRect().left < 350 ? 0 : null;
    }
  }
  window.addEventListener("resize", handleMiniCartPosition);
  function createNavOverlay(item, classToRemove) {
    let navClickaway = document.querySelector(`.${strings[2]}`);
    if (navClickaway !== null) {
      navClickaway.parentNode.removeChild(navClickaway);
    }
    navClickaway = document.createElement("div");
    addClass(navClickaway, strings[2]);
    const primaryNav = document.querySelector("header.header");
    primaryNav.parentNode.insertBefore(navClickaway, primaryNav);
    navClickaway.addEventListener("click", () => {
      removeClass(item, classToRemove);
      navClickaway.parentNode.removeChild(navClickaway);
    });
  }

  // assets/js/hfg/hgf.js
  var HFG = function() {
    this.options = {
      menuToggleDuration: 300
    };
    this.init();
  };
  HFG.prototype.init = function(skipSidebar = false) {
    if (skipSidebar === false) {
      const closeButtons = document.querySelectorAll(
        ".close-sidebar-panel .navbar-toggle"
      );
      addEvent(closeButtons, "click", () => {
        this.toggleMenuSidebar(false);
      });
    }
    const menuMobileToggleButtons = document.querySelectorAll(
      ".menu-mobile-toggle"
    );
    addEvent(menuMobileToggleButtons, "click", (event) => {
      this.toggleMenuSidebar(true, event.target);
    });
    const overlay = document.querySelector(".header-menu-sidebar-overlay");
    if (overlay) {
      addEvent(
        overlay,
        "click",
        function() {
          this.toggleMenuSidebar(false);
        }.bind(this)
      );
    }
  };
  HFG.prototype.toggleMenuSidebar = function(toggle, target = null) {
    const TOGGLE_CLASS_CONTAINER = ".menu-mobile-toggle";
    const buttonsContainer = document.querySelectorAll(TOGGLE_CLASS_CONTAINER);
    removeClass(document.body, "hiding-header-menu-sidebar");
    if (toggle === false) {
      const navClickaway = document.querySelector(".nav-clickaway-overlay");
      if (navClickaway !== null) {
        navClickaway.parentNode.removeChild(navClickaway);
      }
      addClass(document.body, "hiding-header-menu-sidebar");
      removeClass(document.body, "is-menu-sidebar");
      removeClass(buttonsContainer, "is-active");
      setTimeout(
        function() {
          removeClass(document.body, "hiding-header-menu-sidebar");
        }.bind(this),
        1e3
      );
    } else {
      addClass(document.body, "is-menu-sidebar");
      addClass(buttonsContainer, "is-active");
      if (target) {
        document.dispatchEvent(
          new CustomEvent(STX_FOCUS_TRAP_START, {
            detail: {
              container: document.getElementById(
                "header-menu-sidebar"
              ),
              close: ".close-sidebar-panel button",
              firstFocus: "a,input",
              backFocus: target
            }
          })
        );
      }
    }
  };

  // assets/js/hfg/index.js
  function run() {
    window.HFG = new HFG();
    initNavigation();
  }
  function onResizeDebouncedRun() {
    repositionDropdowns();
  }
  window.addEventListener("load", () => {
    run();
  });
  var staxResizeTimeout;
  window.addEventListener("resize", () => {
    clearTimeout(staxResizeTimeout);
    staxResizeTimeout = setTimeout(onResizeDebouncedRun, 500);
  });
})();
