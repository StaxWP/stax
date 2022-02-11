import PropTypes from "prop-types";

const RichText = ({ onChange, currentValue, label, id, editorId }) => {
	return (
		<div className="stax-white-background-control stax-rich-text">
			<span className="customize-control-title">{label}</span>
			<textarea
				id={editorId}
				data-customize-setting-link={id}
				className="stax-custom-html-control-tinymce-editor"
				value={currentValue}
				onChange={({ target: { value } }) => onChange(value)}
			/>
		</div>
	);
};

RichText.propTypes = {
	id: PropTypes.string.isRequired,
	editorId: PropTypes.string.isRequired,
	label: PropTypes.string.isRequired,
	onChange: PropTypes.func.isRequired,
	currentValue: PropTypes.string.isRequired,
};

export default RichText;
