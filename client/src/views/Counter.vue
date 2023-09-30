<template>
  <div class="face">
    <div class="twelve dot"></div>
    <div class="one dot"></div>
    <div class="two dot"></div>
    <div class="three dot"></div>
    <div class="four dot"></div>
    <div class="five dot"></div>
    <div class="six dot"></div>
    <div class="seven dot"></div>
    <div class="eight dot"></div>
    <div class="nine dot"></div>
    <div class="ten dot"></div>
    <div class="eleven dot"></div>
    <div
      class="second-hand"
      :style="`transform: rotate(${secondsPosition}deg) translateY(-68px) `"
    ></div>
    <div class="minute-hand" :style="`transform: rotate(${minutePosition}deg) translateY(-50px)`"></div>
    <div class="hour-hand" :style="`transform: rotate(${hourPosition}deg) translateY(-25px)`"></div>
    <div class="digital-display">{{ count }}</div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from "vue";
import dayjs from "dayjs";

let count = $ref(dayjs().format(`hh:mm:ss`));
let secondsPosition = $ref(dayjs().second() * 6);
let minutePosition = $ref(dayjs().minute() * 6);
let hourPosition = $ref(dayjs().hour() * 30);

onMounted(() => {
  const interval = setInterval(() => {
    count = dayjs().format(`hh:mm:ss`);
    secondsPosition = (dayjs().second() * 6);
    minutePosition = (dayjs().minute() * 6);
    hourPosition = (dayjs().hour() * 30);
  }, 1000);

  onUnmounted(() => clearInterval(interval));
});
</script>

<style>
.dot {
  width: 8px;
  height: 8px;
  border-radius: 100%;
  background: black;
  position: fixed;
}

.twelve {
  transform: rotate(0deg) translateY(-100px);
  width: 12px;
  height: 12px;
}

.one {
  transform: rotate(30deg) translateY(-100px);
}

.two {
  transform: rotate(60deg) translateY(-100px);
}

.three {
  transform: rotate(90deg) translateY(-100px);
  width: 12px;
  height: 12px;
}

.four {
  transform: rotate(120deg) translateY(-100px);
}

.five {
  transform: rotate(150deg) translateY(-100px);
}

.six {
  transform: rotate(180deg) translateY(-100px);
  width: 12px;
  height: 12px;
}

.seven {
  transform: rotate(210deg) translateY(-100px);
}

.eight {
  transform: rotate(240deg) translateY(-100px);
}

.nine {
  transform: rotate(270deg) translateY(-100px);
  width: 12px;
  height: 12px;
}

.ten {
  transform: rotate(300deg) translateY(-100px);
}

.eleven {
  transform: rotate(330deg) translateY(-100px);
}

.face {
  width: 240px;
  height: 240px;
  border-radius: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #000000;
}

.digital-display {
  border: 2px solid #2d3748;
  padding: 0.25rem 0.5rem;
  background: #adb5bd;
  position: fixed;
  z-index: 40;
  font-weight: bolder;
  border-radius: 4px;
}

.second-hand {
  height: 98px;
  width: 2px;
  background: red;
  position: fixed;
  z-index: 10;
}

.minute-hand {
  height: 98px;
  width: 4px;
  background: black;
  position: fixed;
  z-index: 20;
}

.hour-hand {
  height: 72px;
  width: 6px;
  background: black;
  position: fixed;
  z-index: 30;
}
</style>
