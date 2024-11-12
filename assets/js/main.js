/**
* Template Name: Gp
* Updated: Sep 18 2023 with Bootstrap v5.3.2
* Template URL: https://bootstrapmade.com/gp-free-multipurpose-html-bootstrap-template/
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/


(function() {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all)
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener))
      } else {
        selectEl.addEventListener(type, listener)
      }
    }
  }

  /**
   * Easy on scroll event listener 
   */
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  /**
   * Navbar links active state on scroll
   */
  let navbarlinks = select('#navbar .scrollto', true)
  const navbarlinksActive = () => {
    let position = window.scrollY + 200
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return
      let section = select(navbarlink.hash)
      if (!section) return
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        navbarlink.classList.add('active')
      } else {
        navbarlink.classList.remove('active')
      }
    })
  }
  window.addEventListener('load', navbarlinksActive)
  onscroll(document, navbarlinksActive)

  /**
   * Scrolls to an element with header offset
   */
  const scrollto = (el) => {
    let header = select('#header')
    let offset = header.offsetHeight

    let elementPos = select(el).offsetTop
    window.scrollTo({
      top: elementPos - offset,
      behavior: 'smooth'
    })
  }

  /**
   * Toggle .header-scrolled class to #header when page is scrolled
   */
  let selectHeader = select('#header')
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add('header-scrolled')
      } else {
        selectHeader.classList.remove('header-scrolled')
      }
    }
    window.addEventListener('load', headerScrolled)
    onscroll(document, headerScrolled)
  }

  /**
   * Back to top button
   */
  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }

  /**
   * Mobile nav toggle
   */
  on('click', '.mobile-nav-toggle', function(e) {
    select('#navbar').classList.toggle('navbar-mobile')
    this.classList.toggle('bi-list')
    this.classList.toggle('bi-x')
  })

  /**
   * Mobile nav dropdowns activate
   */
  on('click', '.navbar .dropdown > a', function(e) {
    if (select('#navbar').classList.contains('navbar-mobile')) {
      e.preventDefault()
      this.nextElementSibling.classList.toggle('dropdown-active')
    }
  }, true)

  /**
   * Scrool with ofset on links with a class name .scrollto
   */
  on('click', '.scrollto', function(e) {
    if (select(this.hash)) {
      e.preventDefault()

      let navbar = select('#navbar')
      if (navbar.classList.contains('navbar-mobile')) {
        navbar.classList.remove('navbar-mobile')
        let navbarToggle = select('.mobile-nav-toggle')
        navbarToggle.classList.toggle('bi-list')
        navbarToggle.classList.toggle('bi-x')
      }
      scrollto(this.hash)
    }
  }, true)

  /**
   * Scroll with ofset on page load with hash links in the url
   */
  window.addEventListener('load', () => {
    if (window.location.hash) {
      if (select(window.location.hash)) {
        scrollto(window.location.hash)
      }
    }
  });

  /**
   * Preloader
   */
  let preloader = select('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove()
    });
  }

  /**
   * Clients Slider
   */
  new Swiper('.clients-slider', {
    speed: 400,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true
    },
    breakpoints: {
      320: {
        slidesPerView: 2,
        spaceBetween: 40
      },
      480: {
        slidesPerView: 3,
        spaceBetween: 60
      },
      640: {
        slidesPerView: 4,
        spaceBetween: 80
      },
      992: {
        slidesPerView: 6,
        spaceBetween: 120
      }
    }
  });

  /**
   * Porfolio isotope and filter
   */
  window.addEventListener('load', () => {
    let portfolioContainer = select('.portfolio-container');
    if (portfolioContainer) {
      let portfolioIsotope = new Isotope(portfolioContainer, {
        itemSelector: '.portfolio-item'
      });

      let portfolioFilters = select('#portfolio-flters li', true);

      on('click', '#portfolio-flters li', function(e) {
        e.preventDefault();
        portfolioFilters.forEach(function(el) {
          el.classList.remove('filter-active');
        });
        this.classList.add('filter-active');

        portfolioIsotope.arrange({
          filter: this.getAttribute('data-filter')
        });
        portfolioIsotope.on('arrangeComplete', function() {
          AOS.refresh()
        });
      }, true);
    }

  });
  /**
   * Initiate service lightbox 
   */
  const serviceLightbox = GLightbox({
    selector: '.service-lightbox'
  });

  /**
   * service details slider
   */
  new Swiper('.service-details-slider', {
    speed: 400,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true
    }
  });

  /**
   * Testimonials slider
   */
  new Swiper('.testimonials-slider', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true
    }
  });

  /**
   * Animation on scroll
   */
  window.addEventListener('load', () => {
    AOS.init({
      duration: 1000,
      easing: "ease-in-out",
      once: true,
      mirror: false
    });
  });

  /**
   * Initiate Pure Counter 
   */
  new PureCounter();

// table.js

// script.js

// Sample data
const appointments = [
  {
    customerName: "John Doe",
    appointmentDate: "2022-07-01",
    service: "Haircut",
    amount: 30
  },
  {
    customerName: "Jane Smith",
    appointmentDate: "2022-07-02",
    service: "Manicure",
    amount: 20
  }
];

// Function to generate table rows from appointment data
function generateTableRows() {
  const tbody = document.querySelector("tbody");

  appointments.forEach(appointment => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${appointment.customerName}</td>
      <td>${appointment.appointmentDate}</td>
      <td>${appointment.service}</td>
      <td>${appointment.amount}</td>
    `;
    tbody.appendChild(row);
  });
}

// Function to print the receipt
function printReceipt() {
  window.print();
}

// Event listener for the print button
document.getElementById("printButton").addEventListener("click", printReceipt);

// Generate table rows on page load
document.addEventListener("DOMContentLoaded", generateTableRows);

// script.js

document.addEventListener("DOMContentLoaded", function() {
  const amountInput = document.getElementById("amount");
  const methodSelect = document.getElementById("method");
  
  const cashContainer = document.getElementById("cashContainer");
  const ewalletContainer = document.getElementById("ewalletContainer");
  
  methodSelect.addEventListener("change", function() {
    if (this.value === "cash") {
      cashContainer.style.display = "block";
      ewalletContainer.style.display = "none";
    } else if (this.value === "ewallet") {
      cashContainer.style.display = "none";
      ewalletContainer.style.display = "block";
    }
  });
  
  const paymentForm = document.getElementById("paymentForm");
  
  paymentForm.addEventListener("submit", function(event) {
    event.preventDefault();
    
    const amount = parseFloat(amountInput.value);
    const paymentMethod = methodSelect.value;
    let tenderedAmount = 0;
    let ewalletId = "";
    
    if (paymentMethod === "cash") {
      const cashTendered = document.getElementById("cashTendered");
      tenderedAmount = parseFloat(cashTendered.value);
    } else if (paymentMethod === "ewallet") {
      const ewalletIdInput = document.getElementById("ewalletId");
      ewalletId = ewalletIdInput.value;
    }
    
    const change = tenderedAmount - amount;
    
    const receiptContainer = document.createElement("div");
    receiptContainer.className = "receipt-container";
    
    const receiptHeading = document.createElement("h2");
    receiptHeading.innerText = "Receipt";
    
    const receiptDetail1 = document.createElement("p");
    receiptDetail1.className = "receipt-detail";
    receiptDetail1.innerText = "Payment Method: " + paymentMethod;
    
    const receiptDetail2 = document.createElement("p");
    receiptDetail2.className = "receipt-detail";
    receiptDetail2.innerText = "Amount: $" + amount.toFixed(2);
    
    const receiptDetail3 = document.createElement("p");
    receiptDetail3.className = "receipt-detail";
    receiptDetail3.innerText = "Tendered Amount: $" + tenderedAmount.toFixed(2);
    
    const receiptDetail4 = document.createElement("p");
    receiptDetail4.className = "receipt-detail";
    receiptDetail4.innerText = "Change: $" + change.toFixed(2);
    
    receiptContainer.appendChild(receiptHeading);
    receiptContainer.appendChild(receiptDetail1);
    receiptContainer.appendChild(receiptDetail2);
    
    if (paymentMethod === "cash") {
      receiptContainer.appendChild(receiptDetail3);
    } else if (paymentMethod === "ewallet") {
      receiptContainer.appendChild(receiptDetail4);
      const receiptDetail5 = document.createElement("p");
      receiptDetail5.className = "receipt-detail";
      receiptDetail5.innerText = "E-wallet ID: " + ewalletId;
      receiptContainer.appendChild(receiptDetail5);
    }
    
    document.body.appendChild(receiptContainer);
  });
});

})()