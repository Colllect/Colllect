<template>
	<div class="c-colllect-select" :class="classes">
		<label :for="id" class="c-colllect-select--label" v-html="label">
		</label>

		<div class="c-colllect-select--select-border-wrap">
			<select :id="id" v-model="localValue"
							:disabled="disabled"
							class="c-colllect-select--select" @focus="focus" @blur="blur" @mousedown="mouseDown">
				<slot></slot>
			</select>

			<span class="c-colllect-select--arrow"></span>

			<span class="c-colllect-select--error" v-if="!disabled && errored && errorMessage">
				{{ errorMessage }}
			</span>
		</div>
	</div>
</template>

<script src="./select.ts" type="ts"/>
<style src="./select.scss" type="scss"/>
