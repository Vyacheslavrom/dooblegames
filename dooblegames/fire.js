var lever = false; // Тумблер
var textBrightness = 80; // Яркость текста
var fireCount = 9; // Величина пламени
var fireDelta = new Array();
var step = 10; // Шаг колебаний
var angle = 0; // Угол колебаний
var radius = 10;

function animate()
{
fireDelta[fireCount - step] = Math.random() * 2 - 1;
var e = document.getElementById("fire");
var s = "";
for (var i = 0; i < fireCount; i++) { if (s) s += ", ";
s += Math.round(fireDelta[(i + fireCount - step) % fireCount] * i) + "px " + (-2 * i -1) + "px " + (2 + i) + "px ";
s += "rgb(255, " + (255 - i * Math.floor(255 / (fireCount - 1))) + ", 0)";
}
e.style.textShadow = s;
e.style.color = "rgb(" +
(textBrightness + step % 2) + ", " +
textBrightness + ", " +
textBrightness + ")";
step = (step + 1) % fireCount;
angle -= 0.8;

if (angle <= 0) angle = Math.PI * 2; var e = document.getElementById("rgb");
var s = Math.round(Math.cos(angle + Math.PI * 2 / 3) * radius) + "px 4px #0F0";
e.style.textShadow = s;
e.style.color = "rgb(" + (255 - step % 2) + ", 255, 255)";
}

function toggleAnimation()
{
for (var i = 0; i < fireCount; i++) fireDelta[i] = Math.random() * 2 - 1; if (lever)
{
window.clearInterval(lever);
lever = false;
}
else
lever = window.setInterval(function() { animate(); }, 100);
return false;
}
toggleAnimation();
