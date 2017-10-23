<style src="./header.scss" lang="scss"></style>

<template>
  <header>
    <router-link :to="{ name: 'inbox' }" class="header--logo">
      <svg viewBox="0 0 100 100" class="header--logo-image">
        <use xlink:href="#logo"></use>
      </svg>
      Colllect
    </router-link>

    <nav class="header--menu">
      <ul>
        <li>
          <router-link :to="{ name: 'inbox' }" class="header--menu-item">Inbox</router-link>
        </li>
        <li>
          <router-link :to="{ name: 'collections' }" class="header--menu-item">Collections</router-link>
        </li>
      </ul>
    </nav>

    <div class="header--right">
      <ul class="header--right-actions">
        <li>
          <router-link :to="{ name: 'inbox' }" class="header--right-actions-item">Image</router-link>
        </li>
        <li>
          <router-link :to="{ name: 'collections' }" class="header--right-actions-item">Texte</router-link>
        </li>
        <li>
          <router-link :to="{ name: 'inbox' }" class="header--right-actions-item">Link</router-link>
        </li>
        <li>
          <router-link :to="{ name: 'collections' }" class="header--right-actions-item">Color</router-link>
        </li>
      </ul>
      <div class="header--right-user">
        Alex-D
      </div>
    </div>
  </header>
</template>
