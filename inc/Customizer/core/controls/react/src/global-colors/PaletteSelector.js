import { Button, Modal } from "@wordpress/components";
import { useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { trash } from "@wordpress/icons";
import classnames from "classnames";

const PaletteSelector = ({ values, save }) => {
	const { palettes, activePalette } = values;

	const [isOpenModal, setIsOpenModal] = useState(false);
	const [willDelete, setWillDelete] = useState("");

	const deletePalette = () => {
		const nextValues = { ...values };

		if (activePalette === willDelete) {
			nextValues.activePalette = "light";
		}

		delete nextValues.palettes[willDelete];

		setIsOpenModal(false);
		setWillDelete("");

		save(nextValues);
	};

	const setActivePalette = (id) => {
		const nextValues = { ...values };
		nextValues.activePalette = id;

		save(nextValues);
	};

	// Reorder the palette keys so we always have first positions used by defaults.
	const orderedPaletteKeys = [
		...Object.keys(values.palettes).filter(
			(paletteSlug) => !palettes[paletteSlug].allowDeletion
		),
		...Object.keys(values.palettes).filter(
			(paletteSlug) => palettes[paletteSlug].allowDeletion
		),
	];

	return (
		<div className="stax-palettes-wrap">
			{orderedPaletteKeys.map((id) => {
				const { colors, allowDeletion, name } = palettes[id];
				const paletteClasses = classnames([
					"stax-global-color-palette-inner",
					{
						active: activePalette === id,
					},
				]);
				return (
					<div key={id} className="stax-global-color-palette">
						{allowDeletion && (
							<>
								<Button
									isLink
									icon={trash}
									iconSize={21}
									className="delete-palette"
									title={__("Remove Palette", "stax")}
									onClick={() => {
										setWillDelete(id);
										setIsOpenModal(true);
									}}
								/>
								{isOpenModal && (
									<Modal
										isDismissible={false}
										className="stax-global-colors-confirm-delete-modal"
										title={sprintf(
											// translators: %s - name of palette that will be deleted.
											__(
												'Do you really want to delete the "%s" palette?',
												"stax"
											),
											palettes[willDelete].name
										)}
									>
										<p>
											{__(
												'If this is your active palette, the palette will be automatically switched to "Light".',
												"stax"
											)}
										</p>
										<div className="actions">
											<Button
												isPrimary
												icon="trash"
												onClick={deletePalette}
											>
												{__("Delete", "stax")}
											</Button>
											<Button
												isSecondary
												onClick={() => {
													setIsOpenModal(false);
													setWillDelete("");
												}}
											>
												{__("Cancel", "stax")}
											</Button>
										</div>
									</Modal>
								)}
							</>
						)}
						<button
							className={paletteClasses}
							onClick={(e) => {
								e.preventDefault();
								setActivePalette(id);
							}}
							key={name.toLowerCase()}
						>
							{Object.keys(colors).map((i) => {
								if (colors[i].preview)
									return (
										<div
											className="color"
											key={i}
											style={{
												backgroundColor:
													colors[i].color,
											}}
										/>
									);
							})}
							<span className="title">{name}</span>
						</button>
					</div>
				);
			})}
		</div>
	);
};

export default PaletteSelector;
