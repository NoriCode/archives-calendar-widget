/*
 *  This Script is part of the WordPress plugin "Archives Calendar Widget"
 *  Copyright (C) 2013-2017  Aleksei Polechin (http://alek.be)
 *
 *  Before the version 2.x.x ARCW was using jQuery for navigating different calendar pages
 *  but there were often issues with some WP configuration where jQuery wasn't loaded before ARCW js plugin.
 *
 *  This is written in Vanilla JS to be library dependant and reduce issues with JS loading and just for cleaner code
 *  and because jQuery is not always good. Less code than with jQuery before :D
 *
 *  No JS animations CSS does it well.
 */

var ARCW = function (calendar) {
	var self = this;
	this.calendar = calendar;
	// Elements declarations on init
	this.navigation = this.calendar.querySelector('.calendar-navigation');
	this.navButtons = this.navigation.querySelectorAll('[data-nav]');
	this.title = this.navigation.querySelector('.title');
	// Navigation elements
	this.nav = {
		prev: this.navigation.querySelector('[data-nav="prev"]'),
		next: this.navigation.querySelector('[data-nav="next"]'),
		toggle: this.navigation.querySelector('.nav-toggle'),
		menu: this.navigation.querySelector('.menu')
	};

	// The menu items
	this.navItems = this.nav.menu.querySelectorAll('.nav-item');
	// The pages items
	this.pages = this.calendar.querySelectorAll('.page');

	// MENU TOGGLE EVENTS
	var menutimer;
	/**
	 * Menu mouse enter event function
	 * Remove the timeout set by the leave event to not close the menu
	 */
	this.menuMouseEnterEvent = function () {
		clearTimeout(menutimer);
	};
	/**
	 * Menu mouse leave event function
	 * Sets a timeout to close the menu after n seconds if mouse do not come back
	 */
	this.menuMouseLeaveEvent = function () {
		menutimer = setTimeout(function () {
			self.toggleMenu();
		}, 600);
	};

	// Index of the active page and nav element
	this.active = this.getActiveElementIndex();
	// disable next or prev buttons if needed
	this.toggleDisableNav();
	// add click listeners on navigation buttons
	this.addClickListeners();
};


/**
 * Get the element with `active` class
 * @returns {number}
 */
ARCW.prototype.getActiveElementIndex = function () {
	var index = 0;
	while (!this.pages[index].classList.contains('active')) {
		index++;
	}
	return index;

};


/**
 * Add click event listeners on navigation buttons
 */
ARCW.prototype.addClickListeners = function () {
	var self = this;
	// navigation buttons prev/next
	for (var i = 0; i < self.navButtons.length; i++) {
		// need to add the listener with the value i for element i
		// so pass the i to an auto executable function
		(function (index) {
			var element = self.navButtons[index];
			element.addEventListener("click", function () {
				self.goToPage(element.getAttribute('data-nav'));
				self.setNavigationTitle();
			}, false);
		})(i);
	}

	// menu items
	for (var j = 0; j < self.navItems.length; j++) {
		(function (index) {
			var element = self.navItems[index];
			element.addEventListener("click", function () {
				self.goToPage(index);
				self.toggleMenu();
				self.setNavigationTitle();
			}, false);
		})(j);
	}

	// menu toggle
	this.nav.toggle.addEventListener("click", function () {
		self.toggleMenu();
	}, false);

};


/**
 * get the title of the active nav item and put it into the navigation bar title
 */
ARCW.prototype.setNavigationTitle = function () {
	var self = this;
	var navItem = self.navItems[self.active];
	self.title.innerText = navItem.innerText.trim();
	// set the href of the title if title link is not disabled
	if (self.title.tagName === "A") {
		self.title.setAttribute("href", navItem.getAttribute('data-href'));
	}

};


/**
 * Navigates through different pages of the calendar
 * "next"/"prev" for nav arrows OR a Number of the page when selecting it from menu
 * NOTE: next button should go to the most recent calendar, back button should go back in time
 *       as the most recent item is 0 index and the oldest is in the end of the list
 *       we need to invert the count of the next/prev buttons
 * @param {"next"|"prev"|int} destination
 */
ARCW.prototype.goToPage = function (destination) {
	// if no destination is provided
	if (typeof destination === "undefined") {
		throw new Error('`destination` parameter is required for the navigation');
	}

	var self = this;
	var goto;

	// first we set the active variable to the right value
	if (typeof destination === "string") {
		switch (destination) {
			case 'next':
				// if the active page is 0 (most recent)
				if (self.active === 0) {
					// we have nothing to do
					return;
				}
				// set goto page
				goto = self.active - 1;

				break;
			case 'prev':
				// if the active page is last one (the oldest)
				if (self.active === (self.navItems.length - 1)) {
					// we have nothing to do
					return;
				}
				// set goto page
				goto = self.active + 1;
				break;
		}

	} else {
		// if triggered from the menu destination is already a number
		goto = destination;
	}

	if (goto === self.active) {
		return;
	}

	// then we need to switch the classes from the active element to the one we want to go to
	self.navItems[self.active].classList.remove("active");
	self.navItems[goto].classList.add("active");
	self.pages[self.active].classList.remove("active");
	self.pages[goto].classList.add("active");
	//finally update the active variable
	self.active = goto;
	// disable next or prev buttons if needed
	self.toggleDisableNav();

};


/**
 * Open/Close navigation menu of the calendar
 */
ARCW.prototype.toggleMenu = function () {
	var self = this;
	// check if menu is opened
	var opened = self.nav.menu.classList.contains('opened');

	if (opened) {
		// add `opened` class and mouse event listeners
		var menu = self.nav.menu;
		menu.classList.remove('opened');
		menu.removeEventListener("mouseleave", self.menuMouseLeaveEvent);
		menu.removeEventListener("mouseenter", self.menuMouseEnterEvent);
		// position the menu with active item over the navigation bar
		menu.style.top = -(self.active * self.navigation.offsetHeight) + "px";
	} else {
		// remove `opened` class and mouse event listeners
		self.nav.menu.classList.add('opened');
		self.nav.menu.addEventListener("mouseleave", self.menuMouseLeaveEvent, false);
		self.nav.menu.addEventListener("mouseenter", self.menuMouseEnterEvent, false);
	}

};


/**
 * Disables prev and next navigation buttons when the start or end is reached
 */
ARCW.prototype.toggleDisableNav = function () {
	var self = this;
	// disable Next button when active page is 0 (most recent)
	self.nav.next.disabled = (self.active === 0);
	// disable prev button when active page is the last page (the oldest)
	self.nav.prev.disabled = (self.active === (self.navItems.length - 1));

};


/**
 * On DOM content loaded initialize ARCW js for each calendar present on the page
 */
var onDomContentLoaded = function(){
	// get all the calendars on the page
	var calendars = document.querySelectorAll('.calendar-archives');
	// for each calendar create the ARCW instance
	for(var i = 0; i < calendars.length; i++){
		new ARCW(calendars[i]);
	}
	// once the event is fired remove the listener
	document.removeEventListener("DOMContentLoaded", onDomContentLoaded);
};
document.addEventListener("DOMContentLoaded", onDomContentLoaded);