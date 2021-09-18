<template>
	<transition name="c-colllect-modal__transition">
		<div class="c-colllect-modal" v-if="show" @click.self="close" role="dialog" aria-modal="true" aria-labelledby="c-colllect-modal--title">
			<div class="c-colllect-modal--box" role="dialog" :style="{ width, height }">
				<a11y-focus-trap ref="focusTrap">
					<slot></slot>

					<!-- TODO: Use SVG cross icon -->
					<button class="c-colllect-modal--close" @click="close">
						&times;
					</button>
				</a11y-focus-trap>
			</div>
		</div>
	</transition>
</template>

<script src="./modal.ts" type="ts"/>
<style src="./modal.scss" type="scss"/>

