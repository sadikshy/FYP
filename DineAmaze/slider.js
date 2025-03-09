document.addEventListener("DOMContentLoaded", function() {
    let images = document.querySelectorAll(".slider img");
    let index = 0;

    function slideShow() {
        images.forEach((img, i) => {
            img.style.display = i === index ? "block" : "none";
        });
        index = (index + 1) % images.length;
    }

    setInterval(slideShow, 3000);
});
