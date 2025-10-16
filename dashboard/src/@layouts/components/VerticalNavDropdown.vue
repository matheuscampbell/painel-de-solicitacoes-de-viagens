<template>
    <li class="nav-group " @click="toggleDropdown" :class="{'active':isActive, 'open':isOpen}">
      <div class="nav-group-label">
        <VIcon
          :icon="item.icon"
          class="nav-item-icon"
        />
        <span class="nav-item-title ">{{ item.title }}</span>
        <VIcon
          icon="bx-chevron-right"
          class="nav-item-arrow nav-group-arrow float-end"
        />
      </div>
      <VExpandTransition>
        <ul v-if="isOpen" class="nav-group-children pl-2" style="height: auto;">
          <slot v-once ></slot>
        </ul>
      </VExpandTransition>
    </li>
</template>

<script setup>
import {ref, watch} from 'vue';

import { useRoute } from 'vue-router'
const route = useRoute()


const isOpen = ref(false);
const isActive = ref(false);
const toggleDropdown = () => {
  event.stopPropagation();
  isOpen.value = !isOpen.value;
};

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

isRouteActive()

watch( () => route.path,  () => {
  isRouteActive()
})

function isRouteActive() {
  if (route.path.indexOf(
    props.item.to
  ) !== -1) {
    isActive.value = true;
    isOpen.value = true;
  } else {
    isActive.value = false;
    isOpen.value = false;
  }
}
</script>

<style scoped>
  .layout-nav-type-vertical .layout-vertical-nav .nav-link .router-link-exact-active::after, .layout-nav-type-vertical .layout-vertical-nav .nav-group.active:not(.nav-group .nav-group) > :first-child::after {
    top: 0px;
  }
  .nav-group-label{
    display: flex;
    align-items: center;
  }
</style>

