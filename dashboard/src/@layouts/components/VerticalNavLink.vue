<script setup>

const props = defineProps({
  item: {
    type: null,
    required: true,
  },
})

const route = useRoute()

const isActive = ref(false)


function isRouteActive() {
  if (route.path.indexOf(
    props.item.to
  ) !== -1) {
    isActive.value = true;
  } else {
    isActive.value = false;
  }
}
</script>

<template>
  <li
    :title="item.title"
    class="nav-link"
    :class="{ disabled: item.disable }"
  >
    <Component
      :is="item.to ? 'RouterLink' : 'a'"
      :to="item.to"
      :href="item.href"
      :class="{'router-link-exact-active': isActive}"
    >
      <VIcon
        :icon="item.icon"
        size=16
        class="nav-item-icon"
      />
      <!-- 👉 Title -->
      <span class="nav-item-title">
        {{ item.title }}
      </span>
    </Component>
  </li>
</template>

<style lang="scss">
.layout-vertical-nav {
  .nav-link a {
    display: flex;
    align-items: center;
    cursor: pointer;
  }
}
</style>
