<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
import {useUserStore} from "@/store/UserStore";

const userStore = useUserStore()

</script>

<template>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    color="success"
    bordered
  >
    <VAvatar
      class="cursor-pointer"
      color="primary"
      variant="tonal"
    >
      {{ userStore.userInitialLetters() }}
      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
                    :text="userStore.userInitialLetters()"
                  />
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ userStore.user.name }}
            </VListItemTitle>
            <VListItemSubtitle>
              {{ userStore.user?.tipo_usuario }}
            </VListItemSubtitle>
          </VListItem>
          <VDivider class="my-2" />
          <!-- 👉 Logout -->
          <VListItem to="/login">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="bx-log-out"
                size="22"
              />
            </template>

            <VListItemTitle>Logout</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
