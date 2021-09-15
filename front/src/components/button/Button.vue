<template>
	<button class="c-colllect-button" :class="classes" type="button" @click="onClick" @mouseup="blur">
		<slot></slot>
	</button>
</template>

<script src="./button.ts" lang="ts"/>
<style src="./button.scss" lang="scss"/>
