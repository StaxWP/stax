import PropTypes from "prop-types";

const FontPreviewLink = ({ fontFace, onClick, label }) => {
	const style = { fontFamily: fontFace + ", sans-serif" };

	return (
		<a
			href="#font-list"
			onClick={(e) => {
				e.stopPropagation();
				onClick();
			}}
		>
			<span className="stax-font-family">{label || fontFace}</span>
			<span className="stax-font-preview" style={style}>
				Abc
			</span>
		</a>
	);
};

FontPreviewLink.propTypes = {
	fontFace: PropTypes.string.isRequired,
	onClick: PropTypes.func.isRequired,
	label: PropTypes.string,
};

export default FontPreviewLink;
