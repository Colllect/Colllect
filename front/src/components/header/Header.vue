<style src="./header.scss" lang="scss"></style>

<template>
  <header>
    <router-link :to="{ name: 'inbox' }" class="header--logo">Collect.io</router-link>

    <nav class="header--menu">
      <ul>
        <li>
          <router-link :to="{ name: 'inbox' }" class="header--menu-item">Inbox</router-link>
          <router-link :to="{ name: 'collections' }" class="header--menu-item">Collections</router-link>
        </li>
      </ul>
    </nav>

    <div class="header--user">
      Alex-D
    </div>
  </header>
</template>
