<template>
	<div class="p-colllect-styleguide">
		<h1>Styleguide</h1>

		<div class="p-colllect-styleguide--block">
			<h2>Form components</h2>
			<div class="p-colllect-styleguide--form">
				<div class="p-colllect-styleguide--form-fields-column">
					<colllect-input type="email" v-model="inputEmailValue" placeholder="john.doe@example.com" :errored="isEmailErrored" error-message="Please enter a valid email address">Email address</colllect-input>
					<colllect-input type="email" v-model="inputEmailValue" placeholder="Disabled" :errored="isEmailErrored" error-message="Please enter a valid email address" disabled="disabled">Disabled field</colllect-input>
					<colllect-input type="password" autocomplete="new-password" v-model="inputPasswordValue">Account password</colllect-input>
					<colllect-checkbox v-model="checkboxRememberMeValue">Remember me</colllect-checkbox>
					<colllect-checkbox disabled="disabled">Remember me</colllect-checkbox>
					<colllect-radio-group v-model="radioValue">
						<colllect-radio value="image">Image</colllect-radio>
						<colllect-radio value="link">Link</colllect-radio>
						<colllect-radio value="colors">Colors</colllect-radio>
						<colllect-radio value="note">Note</colllect-radio>
						<colllect-radio value="font" disabled="disabled">Font (not implemented yet)</colllect-radio>
					</colllect-radio-group>
					<colllect-select v-model="selectValue" label="Location">
						<option value="FR">France</option>
						<option value="UK">United Kingdom</option>
						<option value="RU">Россия</option>
						<option value="ES" disabled>España</option>
					</colllect-select>
					<br>
					<colllect-button>Submit</colllect-button>
					<colllect-button type="light">Light</colllect-button>
					<colllect-button disabled="disabled">Disabled</colllect-button>
				</div>
				<div class="p-colllect-styleguide--form-fields-values-column">
					Email: <strong>{{ inputEmailValue }}</strong> <br> <br>
					Password: <strong>{{ inputPasswordValue }}</strong> <br> <br>
					Remember me: <strong>{{ checkboxRememberMeValue ? 'Yes' : 'No' }}</strong> <br> <br>
					Element type: <strong>{{ radioValue }}</strong> <br> <br>
					Country: <strong>{{ selectValue }}</strong> <br> <br>
				</div>
			</div>
		</div>

		<div class="p-colllect-styleguide--block">
			<h2>Fonts</h2>

			<div class="p-colllect-styleguide--font" v-for="sassVariableName, font in fonts">
				<span class="p-colllect-styleguide--font-value" :style="{font: font}">{{ font.split('"')[1] + ' (' + font.split('"')[0].trim() + ')' }}</span>
				{{ sassVariableName }}
			</div>
		</div>

		<div class="p-colllect-styleguide--block">
			<h2>Colors</h2>

			<div class="p-colllect-styleguide--colors">
				<div class="p-colllect-styleguide--color-group" v-for="colors in colorLists">
					<div class="p-colllect-styleguide--color" v-for="(sassVariableName, color) in colors" :style="{backgroundColor: color}">
						<span class="p-colllect-styleguide--color-value">{{ color }}</span>
						<span class="p-colllect-styleguide--color-name">{{ sassVariableName }}</span>
					</div>
				</div>
			</div>
		</div>

		<div class="p-colllect-styleguide--block">
			<h2>Gradients</h2>

			<div class="p-colllect-styleguide--colors">
				<div class="p-colllect-styleguide--color-group">
					<div class="p-colllect-styleguide--color" :style="{background: `linear-gradient(to bottom right, #ffcfa9, #e671a5)`}">
						<span class="p-colllect-styleguide--color-value">#ffcfa9 > #e671a5</span>
					</div>
					<div class="p-colllect-styleguide--color" :style="{background: `linear-gradient(to bottom right, #b49edc, #769bf7)`}">
						<span class="p-colllect-styleguide--color-value">#b49edc > #769bf7</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script src="./styleguidePage.ts" type="ts"/>
<style src="./styleguidePage.scss" type="scss"/>

