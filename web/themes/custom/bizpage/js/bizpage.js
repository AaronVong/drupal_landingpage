const openMenuBtn = document.querySelector(".menu-toggle-target-show");
const closeMenuBtn = document.querySelector(".menu-toggle-target-hide");
const primaryMenu = document.querySelector(".menu--main .content .menu");
const menuLayer = document.querySelector(".menu--main--layer");
const heroCarouselLeftControl = document.querySelector(".carousel-control--left");
const heroCarouselRightControl = document.querySelector(".carousel-control--right");
const heroCarouselSlides = document.getElementsByClassName("carousel-slide");
const heroCarouselDots = document.getElementsByClassName("carousel-dot");
const heroCarouselSlideContents = document.querySelectorAll(".hero-carousel .carousel-slide .views-field-body .field-content p");
const expandDropdownMenuBtn = document.querySelectorAll(".menu--main .menu .menu-item--expanded");
const skillBars = document.querySelectorAll(".skill-list li.skill-item");
const factNumbers = document.querySelectorAll(".fact-list li .views-field-field-fact-number .field-content");
let carouselCounter = 1;
const CAROUSEL_LENGTH = heroCarouselSlides.length;
const NUMBER_INCREMENT_SPEED = 10;
const NUMBER_INCREMENT_STEP = 5;
let IO_OPTIONS = {
  rootMargin: '100px',
  threshold: 1.0
}
const BASIC_SLICK_OPTIONS = {
  centerMode : true,
  dots: true,
  arrows: false,
  dotsClass: "slick-dots--custom",
  swipe: true,
  draggable: true,
  mobileFirst: true,
  variableWidth: true,
}
let contentObserver = new IntersectionObserver(slideUpItemObserver, IO_OPTIONS);
let regionBlockListItemObserver = new IntersectionObserver(slideUpItemObserver, IO_OPTIONS);
let skillLevelObserver = new IntersectionObserver(handleSkillLevelObserver, {...IO_OPTIONS, rootMargin: "0px"});
let numberObserver = new IntersectionObserver((entries, observer)=>{
  entries.forEach(entry => {
    incEltNbr(null, entry.target);
    if(entry.isIntersecting){
      observer.unobserve(entry.target);
    }
  })
},{...IO_OPTIONS, rootMargin: "0px"});

/* Change navigation background on scroll */
function changeNavigationBg(){
  return;
  const navContainer= document.getElementsByClassName("primary-menu-container")[0];
  const scrollTop = window.scrollY;
  if(scrollTop === 0){
    navContainer.style.backgroundColor = "transparent";
  }
  if(scrollTop >= 20) {
    navContainer.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
  }
}

/* Make slide up/down on scroll for element */
function slideUpItemObserver(entries, observer){
  entries.forEach(entry => {
    entry.target.classList.toggle("show-slide-content", entry.isIntersecting);
    if(entry.isIntersecting){
      observer.unobserve(entry.target);
    }
  })
}

/* Make progress animation for skill bar level*/
function handleSkillLevelObserver(entries, observer){
  entries.forEach(entry => {
    const percent = entry.target.getAttribute("percent");
    entry.target.style.width = percent + "%";
    if(entry.isIntersecting){
      observer.unobserve(entry.target);
    }
  })
}


/**
 *
 * @param e
 * Handle Dropdown for sub-menu
 */
function dropdownMenu(e){
  const link = e.currentTarget;
  const submenu = link.nextElementSibling;
  const submenuHeight = submenu.clientHeight;

  if(submenuHeight <= 0){
    console.log(submenuHeight);
    submenu.style.height = "auto";
  }else{
    console.log(submenuHeight);
    submenu.style.height = "0px";
  }
}

/**
 * Handle Open main-menu
 */
openMenuBtn.addEventListener("click", (e) => {
  e.preventDefault();
  openMenuBtn.classList.add("hide");
  closeMenuBtn.classList.remove("hide");
  primaryMenu.style.display="flex";
});

/**
 * Handle close main-menu
 */
closeMenuBtn.addEventListener("click", (e) => {
  e.preventDefault();
  openMenuBtn.classList.remove("hide");
  closeMenuBtn.classList.add("hide");
  primaryMenu.style.display = "none";
});


/**
 *
 * @param id
 * @param ele
 * Select element to have counting animation with element ID or HTML element
 */
function incEltNbr(id=null, ele = null) {
  elt = ele || document.getElementById(id);
  endNbr = Number(elt.outerText);
  incNbrRec(0, endNbr, elt);
}

/**
 * A recursive function to increase the number.
 */
function incNbrRec(i, endNbr, elt) {
  if (i <= endNbr) {
    elt.innerHTML = i;
    setTimeout(function() {//Delay a bit before calling the function again.
      incNbrRec(i + NUMBER_INCREMENT_STEP, endNbr, elt);
    }, NUMBER_INCREMENT_SPEED);
  }
}

/**
 * Document ready
 */
jQuery(document).ready(()=>{
  /**
   * Add event click to every Hero Carousel Dots
   */
  Array.from(heroCarouselDots).forEach(dot => dot.addEventListener("click", (e)=>{
    plusSlides(1);
  }))

  /**
   * Observer Hero Carousel content for animation
   */
  heroCarouselSlideContents.forEach(item => {
    contentObserver.observe(item);
  })

  /**
   * Add click event for main-menu item to expand sub-menu
   */
  expandDropdownMenuBtn?.forEach(li =>{
    li.firstElementChild.addEventListener("click", (e)=>{
      dropdownMenu(e)
    })
  })

  document.querySelectorAll(".region ul li").forEach(li => {
    regionBlockListItemObserver.observe(li);
  })

  window.addEventListener("scroll", changeNavigationBg);

  /**
   * Percent bar for Skill region and observer it for aniamtion
   */
  skillBars?.forEach((li, index) => {
    const percent = jQuery(`.skill-list li:nth-child(${index+1}) .field_skill_level`).text()
    const colorCode = Math.floor(Math.random()*16777215).toString(16);
    const percentBar = document.createElement("div");
    percentBar.setAttribute("percent", percent)
    percentBar.classList.add("percent-bar");
    percentBar.style.backgroundColor = `#${colorCode}`;
    li.appendChild(percentBar);
    skillLevelObserver.observe(percentBar);
  });

  /**
   * Observer number for counting animation
   */
  factNumbers?.forEach(item => {
    numberObserver.observe(item);
  });

  /**
   * Slick Carousel for client list
   */
  jQuery(".client-list").slick({
    ...BASIC_SLICK_OPTIONS,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 5,
          slidesToScroll: 3,
          infinite: true,
        }
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 640,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });

  /**
   * Slick carousel for testimonial list
   */
  jQuery(".testimonial-list").slick({...BASIC_SLICK_OPTIONS, variableWidth: false, variableHeight: true});
  jQuery(".portfolio-images-list").slick({...BASIC_SLICK_OPTIONS, variableWidth: false, variableHeight: false, centerMode: false, autoplay: true, autoplaySpeed : 2000});
  jQuery(".views-exposed-form").submit(()=>{
    console.log("submitted");
  })

  jQuery(".node-landing_page-content > ul.field_hero_carousel").slick({
    ...BASIC_SLICK_OPTIONS,
    arrows: true,
    draggable: false,
    swipe: false,
    variableHeight: true,
    centerMode: false,
    appendArrows: "ul.field_hero_carousel",
    appendDots: "ul.field_hero_carousel",
  })
})





