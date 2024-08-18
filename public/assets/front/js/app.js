const navbar = document.querySelector("#header");

var sticky = navbar.offsetTop;

function addScrolledClass() {
    if (window.pageYOffset  > sticky) {
        navbar.classList.add("scrolled");
    } else {
        navbar.classList.remove("scrolled");
    }
}

window.onscroll = function () {
    addScrolledClass();
};

let getUrI = window.location.pathname.split("/");
let getPageUrl = getUrI.at(-1);

// console.log(getPageUrl, getUrI);

if (getPageUrl == "terms-condition") {
    document
        .querySelector("span.terms")
        .classList.replace("text-white", "text-primary");
} else if (getPageUrl == "privacy-policy") {
    document
        .querySelector("span.privacy")
        .classList.replace("text-white", "text-primary");
} else {
    document.querySelector("span.terms").classList.remove("text-primary");

    document.querySelector("span.privacy").classList.remove("text-primary");
}
const CurrentLocation = location.href;
const menuItem = document.querySelectorAll(".menu-links-wrapper a.menu-links");
const menuLength = menuItem.length;
for (let i = 0; i < menuLength; i++) {
    if (menuItem[i].href === CurrentLocation) {
        // menuItem[i].className = "active";
        menuItem[i].classList.add("active");
    }
}
function phoneMask() {
    var num = $(this).val().replace(/\D/g, "");
    $(this).val(
        "+" +
            num.substring(0, 1) +
            "(" +
            num.substring(1, 4) +
            ")" +
            num.substring(4, 7) +
            "-" +
            num.substring(7, 11)
    );
}

//
// app js
$(document).ready(function () {
    $('[type="tel"]').keyup(phoneMask);
    // Mobile Nav
    $("header .canvas-icon i").click(function () {
        $("header .mobile-header").addClass("show");
    });

    $("header .mobile-header .cancel").click(function () {
        $("header .mobile-header").removeClass("show");
    });
    // Mobile Nav
});
// video slider starts here
$(".video-slider-wrapper").slick({
    slidesToShow: 1,
    // autoplay: true,
    dots: true,
    arrows: false,
    // autoplaySpeed: 2000,
});
// video slider ends here
$(".slide-wrapper").slick({
    slidesToShow: 1,
    // autoplay: true,
    dots: false,
    arrows: true,
});
// profile
$(".edit").click(function () {
    $(".edit-block").css("display", "block");
    $(".profilr-detail-wrapper-content").css("display", "none");
});
$(".change-password").click(function () {
    $(".edit-wrapper.edit-block").css("display", "none");
    $(".profilr-detail-wrapper-content").css("display", "block");
});

// animation
$(".nav-pills button").click(function () {
    var position = $(this).parent().position();
    var height = $(this).parent().height();
    $(".nav-pills .slider").css({
        top: +position.top,
        height: height,
    });
});
var actheight = $(" .nav-pills").find(".active").parent("li").height();
var actPosition = $(" .nav-pills .active").position();
$(".nav-pills .slider").css({
    top: +actPosition.top - 10,
    height: actheight,
});
// upload
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#imagePreview").css(
                "background-image",
                "url(" + e.target.result + ")"
            );
            $("#imagePreview").hide();
            $("#imagePreview").fadeIn(650);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
$("#imageUpload").change(function () {
    readURL(this);
});
// profile

// active footer links starts here
