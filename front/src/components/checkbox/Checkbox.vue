<template>
	<div class="c-colllect-checkbox" :class="classes">
		<!-- The outer div above avoid elements on the right of the checkbox -->

		<label :for="id" class="c-colllect-checkbox--label" @mouseup="blur">
			<slot></slot>
		</label>

		<input type="checkbox" :id="id" v-model="checked"
					 :disabled="disabled" @mouseup="blur"
					 class="c-colllect-checkbox--input">
		<span class="c-colllect-checkbox--checkbox">
    <span class="c-colllect-checkbox--checkbox-tick"></span>
  </span>
	</div>
</template>

<script src="./checkbox.ts" type="ts"/>
<style src="./checkbox.scss" type="scss"/>
