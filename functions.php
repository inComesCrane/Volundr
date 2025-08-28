<?php
function isDefinitionValidWeapon($itemDefinition) {
		return (isset($itemDefinition->collectibleHash) && isset($itemDefinition->defaultDamageTypeHash));
	}

	const ITEMCATEGORY_WEAPON_PERKS = 610365472;
	function isDefinitionValidWeaponPerk($itemDefinition) {
		// Explanation for this array in the WeaponPerk constructor
		$perkHashExceptions = [
				"Grenade Chaser", // Quicksilver Storm perk (Lightfall preorder)
				"Horizon Ironsights" // No Land Beyond perk  (Edge of Fate preorder)
			];
		if (!isset($itemDefinition->perks[0]) && !in_array($itemDefinition->displayProperties->name, $perkHashExceptions)) {
			return false;
		}

		return (int)$itemDefinition->inventory->bucketTypeHash == BUCKET_CONSUMABLES
			&& ($itemDefinition->itemTypeDisplayName ?? "") == "Trait"
			&& in_array(ITEMCATEGORY_WEAPON_PERKS, $itemDefinition->itemCategoryHashes);
	}

	/*
		Checks if the item is a weapon mod
		Excludes deprecated mods
	*/
	function isDefinitionValidWeaponMod($itemDefinition) {

		$plugIdentifier = isset($itemDefinition->plug) ? $itemDefinition->plug->plugCategoryIdentifier : 'noplug';

		return preg_match('/v[0-9]{3,}\.weapon\.mod_[a-z]+/', $plugIdentifier)
			&& !str_contains($plugIdentifier, "empty");
	}

	/*
		Checks if the item is an armor perk
	*/
	function isDefinitionValidArmorPerk($itemDefinition) {
		/*
			perhaps: itemTypeDisplayName == Intrinsic
			and
			itemTypeAndTierDisplayName == Exotic Intrinsic
			ok so for some weird reason this also has weapon mod categories :/
			this is a problem for future ruse

			and
			itemCategoryHashes = 59 (aka "Mods")
			and
			itemCategoryHashes = 2237038328 (aka "Weapon Mods: Intrinsic")
			and
			itemCategoryHashes = 610365472 (aka "Weapon Mods")

			bucketTypeHash:3313201758 // <InventoryBucket "Modifications">
			interesting that for exotic weapon perks, it's bucketTypeHash:1469714392 // <InventoryBucket "Consumables"> instead
			tierTypeHash:2759499571 // <ItemTierType "Exotic">
		*/
		return false;
	}

	/*
		Checks if the item is an artifact perk from the current season
	*/
	function isDefinitionValidArtifactPerk($itemDefinition) {

		$plugIdentifier = isset($itemDefinition->plug) ? $itemDefinition->plug->plugCategoryIdentifier : 'noPlug';
		$itemTypeDisplayName = isset($itemDefinition->itemTypeDisplayName) ? $itemDefinition->itemTypeDisplayName : 'noTypeDisplay';

		return $itemTypeDisplayName == "Artifact Perk";
		// actualy for this we find the artifact and then just extract the mods from there
		// smth easy for once
		// return preg_match('/v[0-9]{3,}\.weapon\.mod_[a-z]+/', $plugIdentifier)
		// 	&& !str_contains($plugIdentifier, "empty");
	}