/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

/**
 * React components used to add interactive elements in the editor.
 */
import { PanelBody, TextControl } from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes }) {
	const { submitText } = attributes;
	let displaySubmitText = submitText !== '' ? submitText : 'Search';

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'woommy-mmy-block' ) }>
					<TextControl
						label={ __(
							'Submit Text',
							'woommy-mmy-block'
						)}
						value={ submitText }
						onChange={ ( value ) =>
							setAttributes( { submitText: value})
						}
					/>
				</PanelBody>
			</InspectorControls>
			<form id="make-model-year-form" method="get" {...useBlockProps()}>
				<div class="container">
					<select id="make" name="make" required>
						<option value="">Make</option>
					</select>
					<select id="model" name="model" required>
						<option value="">Model</option>
					</select>
					<select id="car-year" name="car-year" required>
						<option value="">Year</option>
					</select>
				</div>
				<div class="wp-block-buttons is-layout-flex wp-block-buttons-is-layout-flex">
					<div class="wp-block-button"><button class="wp-block-button__link wp-element-button" type="submit">{ displaySubmitText }</button></div>
				</div>
			</form>
		</>
	);
}
