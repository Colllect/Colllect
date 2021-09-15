<template>
	<div class="c-colllect-radio" :class="classes">
		<label :for="id" class="c-colllect-radio--label" @mouseup="blur">
			<slot></slot>
		</label>

		<input type="radio" :id="id" v-model="groupValue" :value="value" :name="radioGroupId"
					 :disabled="isDisabled" @mouseup="blur"
					 class="c-colllect-radio--input">
		<span class="c-colllect-radio--radio">
			<span class="c-colllect-radio--radio-dot"></span>
		</span>
	</div>
</template>

<script src="./radio.ts" type="ts"/>
<style src="./radio.scss" type="scss"/>
