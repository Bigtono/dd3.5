// Version avec local storage

"use strict";

(function () {
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  function init() {
    var $ = document.querySelector.bind(document);

    var verticalNav = $(".vertical_nav");
    if (!verticalNav) return;

    var wrapper     = $(".wrapper");
    var menu        = document.getElementById("js-menu");
    var toggler     = $(".toggle_menu");
    var collapseBtn = $(".collapse_menu");

    var itemsWithSub = menu ? menu.querySelectorAll(".menu--item__has_sub_menu") : [];

    // Ouvre/ferme le panneau latéral (mobile < 992px)
    if (toggler) {
      toggler.addEventListener("click", function () {
        verticalNav.classList.toggle("vertical_nav__opened");
        if (wrapper) wrapper.classList.toggle("toggle-content");
      });
    }

    // Minifie le menu et ferme tout
    if (collapseBtn) {
      collapseBtn.addEventListener("click", function () {
        verticalNav.classList.toggle("vertical_nav__minify");
        if (wrapper) wrapper.classList.toggle("wrapper__minify");
        closeAllSubmenus(itemsWithSub);
        localStorage.removeItem("menu_open_id"); // reset
      });
    }

    // Donner un identifiant unique à chaque <li> si pas déjà présent
    itemsWithSub.forEach(function (li, idx) {
      if (!li.id) {
        li.id = "menu-sub-" + idx;
      }
      var trigger = li.querySelector(":scope > .menu--link");
      if (trigger) {
        trigger.setAttribute("role", "button");
        trigger.setAttribute("tabindex", "0");
        trigger.setAttribute("aria-expanded", "false");
      }
    });

    // Réouvrir le sous-menu mémorisé au rechargement
    var savedId = localStorage.getItem("menu_open_id");
    if (savedId) {
      var savedLi = document.getElementById(savedId);
      if (savedLi) {
        openSubmenu(savedLi);
      }
    }

    // Délégation : clic
    if (menu) {
      menu.addEventListener("click", function (e) {
        var trigger = e.target.closest(".menu--item__has_sub_menu > .menu--link");
        if (!trigger) {
          var subLink = e.target.closest(".sub_menu--link");
          if (subLink) {
            var parentSubmenu = subLink.closest(".menu--item__has_sub_menu");
            closeAllSubmenus(itemsWithSub, parentSubmenu || null);
            if (parentSubmenu && parentSubmenu.id) {
              openSubmenu(parentSubmenu);
              localStorage.setItem("menu_open_id", parentSubmenu.id);
            } else {
              localStorage.removeItem("menu_open_id");
            }
            return;
          }

          var plainLink = e.target.closest(".menu--link");
          if (plainLink) {
            closeAllSubmenus(itemsWithSub);
            localStorage.removeItem("menu_open_id");
          }
          return;
        }

        e.preventDefault();
        var li = trigger.parentElement;
        var willOpen = !li.classList.contains("menu--subitens__opened");

        // fermer les autres
        closeAllSubmenus(itemsWithSub, li);

        if (willOpen) {
          openSubmenu(li);
          localStorage.setItem("menu_open_id", li.id);
        } else {
          li.classList.remove("menu--subitens__opened");
          trigger.setAttribute("aria-expanded", "false");
          localStorage.removeItem("menu_open_id");
        }
      });

      // Clavier
      menu.addEventListener("keydown", function (e) {
        var trigger = e.target.closest(".menu--item__has_sub_menu > .menu--link");
        if (!trigger) return;
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          trigger.click();
        }
      });
    }

    function closeAllSubmenus(nodeList, except) {
      nodeList.forEach(function (li) {
        if (li !== except && li.classList.contains("menu--subitens__opened")) {
          li.classList.remove("menu--subitens__opened");
          var t = li.querySelector(":scope > .menu--link");
          if (t) t.setAttribute("aria-expanded", "false");
        }
      });
    }

    function openSubmenu(li) {
      li.classList.add("menu--subitens__opened");
      var trigger = li.querySelector(":scope > .menu--link");
      if (trigger) trigger.setAttribute("aria-expanded", "true");
    }
  }
})();
