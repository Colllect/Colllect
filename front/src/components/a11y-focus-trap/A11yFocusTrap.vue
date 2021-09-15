<template>
	<div ref="rootContainer">
		<div tabindex="0" ref="startElement"></div>
		<div ref="focusableContainer">
			<slot></slot>
		</div>
		<div tabindex="0" ref="endElement"></div>
	</div>
</template>

<script src="./a11yFocusTrap.ts" lang="ts"/>
