import { Path, Rect, SVG } from "@wordpress/components";
import classnames from "classnames";

export const staxIcon = ({ className }) => {
	return (
		<SVG
			width="17"
			height="24"
			viewBox="0 0 58 72"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
			className={classnames(["stax-icon", className])}
		>
			<Path
				d="M40.0227 22.1136C39.7955 19.6136 38.7841 17.6705 36.9886 16.2841C35.2159 14.875 32.6818 14.1705 29.3864 14.1705C27.2045 14.1705 25.3864 14.4545 23.9318 15.0227C22.4773 15.5909 21.3864 16.375 20.6591 17.375C19.9318 18.3523 19.5568 19.4773 19.5341 20.75C19.4886 21.7955 19.6932 22.7159 20.1477 23.5114C20.625 24.3068 21.3068 25.0114 22.1932 25.625C23.1023 26.2159 24.1932 26.7386 25.4659 27.1932C26.7386 27.6477 28.1705 28.0455 29.7614 28.3864L35.7614 29.75C39.2159 30.5 42.2614 31.5 44.8977 32.75C47.5568 34 49.7841 35.4886 51.5795 37.2159C53.3977 38.9432 54.7727 40.9318 55.7045 43.1818C56.6364 45.4318 57.1136 47.9545 57.1364 50.75C57.1136 55.1591 56 58.9432 53.7955 62.1023C51.5909 65.2614 48.4205 67.6818 44.2841 69.3636C40.1705 71.0455 35.2045 71.8864 29.3864 71.8864C23.5455 71.8864 18.4545 71.0114 14.1136 69.2614C9.77273 67.5114 6.39773 64.8523 3.98864 61.2841C1.57955 57.7159 0.340909 53.2045 0.272727 47.75H16.4318C16.5682 50 17.1705 51.875 18.2386 53.375C19.3068 54.875 20.7727 56.0114 22.6364 56.7841C24.5227 57.5568 26.7045 57.9432 29.1818 57.9432C31.4545 57.9432 33.3864 57.6364 34.9773 57.0227C36.5909 56.4091 37.8295 55.5568 38.6932 54.4659C39.5568 53.375 40 52.125 40.0227 50.7159C40 49.3977 39.5909 48.2727 38.7955 47.3409C38 46.3864 36.7727 45.5682 35.1136 44.8864C33.4773 44.1818 31.3864 43.5341 28.8409 42.9432L21.5455 41.2386C15.5 39.8523 10.7386 37.6136 7.26136 34.5227C3.78409 31.4091 2.05682 27.2045 2.07955 21.9091C2.05682 17.5909 3.21591 13.8068 5.55682 10.5568C7.89773 7.30682 11.1364 4.77273 15.2727 2.95455C19.4091 1.13636 24.125 0.22727 29.4205 0.22727C34.8295 0.22727 39.5227 1.14772 43.5 2.98863C47.5 4.80682 50.6023 7.36364 52.8068 10.6591C55.0114 13.9545 56.1364 17.7727 56.1818 22.1136H40.0227Z"
				fill="white"
			/>
		</SVG>
	);
};

export const alignRightIcon = () => {
	return (
		<SVG
			width="17"
			height="18"
			viewBox="0 0 17 18"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
		>
			<Path
				d="M16.5 1.25H7.61111M16.5 9H0.5M16.5 16.75H7.61111"
				strokeWidth="1.5"
			/>
		</SVG>
	);
};

export const alignCenterIcon = () => {
	return (
		<SVG
			width="17"
			height="18"
			viewBox="0 0 17 18"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
		>
			<Path
				d="M12.9444 1.25H4.05556M16.5 9H0.5M12.9444 16.75H4.05556"
				strokeWidth="1.5"
			/>
		</SVG>
	);
};

export const alignLeftIcon = () => {
	return (
		<SVG
			width="17"
			height="18"
			viewBox="0 0 17 18"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
		>
			<Path
				d="M0.5 1.25H9.38889M0.5 9H16.5M0.5 16.75H9.38889"
				strokeWidth="1.5"
			/>
		</SVG>
	);
};
