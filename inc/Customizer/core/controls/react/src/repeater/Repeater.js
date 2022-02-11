import "../scss/_repeater.scss";
import RepeaterItem from "./RepeaterItem";
import PropTypes from "prop-types";
import { Button } from "@wordpress/components";
import { useState } from "@wordpress/element";
import { ReactSortable } from "react-sortablejs";
import { __ } from "@wordpress/i18n";

const Repeater = ({ fields, value, onUpdate }) => {
	const [sorting, setSorting] = useState(false);

	const handleToggle = (index) => {
		const newValue = [...value];
		newValue[index].visibility =
			value[index].visibility === "yes" ? "no" : "yes";
		onUpdate(newValue);
	};

	const handleAddItem = () => {
		const newValue = [...value];
		const newItem = {};

		for (const [field] of Object.entries(newValue[0])) {
			if (field === "visibility") {
				newItem[field] = "yes";
				continue;
			}

			if (typeof value[0][field] === "boolean") {
				newItem[field] = true;
				continue;
			}

			if (fields[field].type === "select") {
				newItem[field] = Object.keys(fields[field].choices)[0];
				continue;
			}
			newItem[field] = "";
		}

		newValue.push(newItem);
		onUpdate(newValue);
	};

	const handleRemove = (index) => {
		const newValue = [...value];
		newValue.splice(index, 1);
		onUpdate(newValue);
	};

	const handleContentChange = (index, newItemValue) => {
		const newValue = [...value];
		newValue[index] = newItemValue;
		onUpdate(newValue);
	};

	return (
		<div className="stx-repeater">
			<ReactSortable
				className="stx-repeater-items-container"
				list={value}
				setList={onUpdate}
				animation={300}
				forceFallback={true}
				handle=".stx-repeater-handle"
			>
				{value.map((val, index) => {
					return (
						<RepeaterItem
							className="stx-repeater-item"
							fields={fields}
							value={value}
							itemIndex={index}
							onToggle={handleToggle}
							onContentChange={(newItemValue) =>
								handleContentChange(index, newItemValue)
							}
							onRemove={handleRemove}
							index={index}
							sorting={sorting}
							key={"repeater-item-" + index}
						/>
					);
				})}
			</ReactSortable>
			<div className="stx-repeater-options">
				{value.length > 1 && (
					<Button
						className="stx-repeater-reorder-button"
						isLink
						onClick={() => {
							setSorting(!sorting);
						}}
					>
						{sorting ? __("Done", "stax") : __("Reorder", "stax")}
					</Button>
				)}
				{!sorting && (
					<Button
						isSecondary
						onClick={handleAddItem}
						className="stx-repeater-add-item-button"
					>
						{__("Add Item", "stax")}
					</Button>
				)}
			</div>
		</div>
	);
};

Repeater.propTypes = {
	value: PropTypes.array.isRequired,
	fields: PropTypes.object.isRequired,
	onUpdate: PropTypes.func.isRequired,
};

export default Repeater;
