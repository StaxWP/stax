import React from 'react';
import {
	createInterpolateElement,
	useEffect,
	useState,
} from '@wordpress/element';
import { Button, Modal } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { info } from '@wordpress/icons';

type Props = {
	builderName: string;
	builder: string;
};

const BuilderHeaderNotification: React.FC<Props> = ({
	builder,
	builderName,
}) => {
	const [modalVisible, setModalVisible] = useState(false);
	const [currentHeaderName, setCurrentHeaderName] = useState(null);

	const toggleModal = () => {
		setModalVisible(!modalVisible);
	};

	const focusConditionalSelector = () => {
		window.wp.customize.control('stax_header_conditional_selector').focus();
	};

	useEffect(() => {
		// @ts-ignore
		const updateName = (e) => {
			setCurrentHeaderName(e.detail);
		};

		document.addEventListener('stx-change-conditional-header', updateName);

		return () =>
			document.removeEventListener(
				'stx-change-conditional-header',
				updateName
			);
	}, []);

	const {
		dashUpdatesMessage,
		instructionalVid,
		hideConditionalHeaderSelector: incompatiblePro,
	} = window.StaxReactCustomize;

	// Disable conditional headers on old versions of the plugin.
	useEffect(() => {
		if (!incompatiblePro || builder !== 'header') {
			return;
		}

		const control = window.wp.customize.control(
			'stax_header_conditional_selector'
		);

		if (control) {
			return;
		}

		const transparentControl = window.wp.customize.control(
			'stax_transparent_header'
		);

		if (!transparentControl) {
			return;
		}

		const sectionToNotify = transparentControl.params.section;
		window.wp.customize.section(sectionToNotify).notifications.add(
			new window.wp.customize.Notification(
				'stax-incompatible-conditional',
				{
					type: 'warning',
					message: dashUpdatesMessage,
				}
			)
		);
	});

	const renderInstruction = () => {
		if (builder !== 'header' || !currentHeaderName) {
			return (
				<p>
					<strong>
						{sprintf(
							/* translators: %1$s builder name (ie: Header) */
							__('%1$s Builder', 'stax'),
							builderName
						) + ':'}
					</strong>
					{__(
						'Click on any empty space to add components, or existing components to adjust settings.',
						'stax'
					)}
				</p>
			);
		}

		return (
			<p>
				{typeof createInterpolateElement !== 'undefined' &&
					createInterpolateElement(
						sprintf(
							/* translators: %1$s conditional header name (ie: Posts Header, Page Header, 404 Header) */
							__(
								'You are customizing the <Button>%1$s</Button> Header',
								'stax'
							),
							currentHeaderName || __('')
						),
						{
							Button: (
								<Button
									isLink
									onClick={focusConditionalSelector}
								>
									#dumptext
								</Button>
							),
						}
					)}
			</p>
		);
	};

	return (
		<span className="builder-instructions">
			{renderInstruction()}
			<Button
				style={{ padding: 0 }}
				isLink
				icon={info}
				iconSize={20}
				onClick={toggleModal}
			/>
			{modalVisible && (
				<Modal
					title={''}
					className="stax-ui-modal-wrap"
					onRequestClose={toggleModal}
					shouldCloseOnClickOutside
					isDismissible
				>
					<video
						style={{ margin: '0 auto', display: 'block' }}
						autoPlay
						muted
						loop
						playsInline
					>
						<source src={instructionalVid} type="video/mp4" />
					</video>
				</Modal>
			)}
		</span>
	);
};

export default BuilderHeaderNotification;
