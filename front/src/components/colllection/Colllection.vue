<template>
	<div class="c-colllect-colllection" :class="classes">
		<h1 class="c-colllect-colllection--title">{{ name }}</h1>

		<div class="c-colllect-colllection--loading" v-if="!isLoaded">
			Loading...
		</div>
		<div class="c-colllect-colllection--empty-state" v-else-if="elements.length === 0">
			Nothing there. Consider add something :)
		</div>
		<div class="c-colllect-colllection--elements" ref="container">
			<colllect-element :element="element" v-for="element in elements" @load="updateGrid"></colllect-element>
		</div>
	</div>
</template>

<script src="./colllection.ts" type="ts"/>
<style src="./colllection.scss" type="scss"/>
