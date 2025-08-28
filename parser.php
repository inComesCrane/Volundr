<?php
	require __DIR__ . '/vendor/autoload.php';
	require __DIR__ . '/utilities.php';
	require __DIR__ . '/classes.php';
	require __DIR__ . '/constants.php';
	require __DIR__ . '/enums.php';
	require __DIR__ . '/functions.php';

	use \JsonMachine\Items;

	// TODO: make these configs
	$currentSeason = 27;
	$latestManifestFileName = "DestinyInventoryItemDefinition-c927add4-d286-4b35-9496-a3a553584307.json";
	$itemDefinitions = Items::fromFile('C:\Users\incomescrane\Desktop\\' . $latestManifestFileName);

	// ARMOR SETS
	$client = new \GuzzleHttp\Client();
	$response = $client->request('GET', 'https://www.bungie.net/common/destiny2_content/json/en/DestinyEquipableItemSetDefinition-6451f787-b065-400f-8522-5629894ee6a0.json');
	$guzzleStream = \GuzzleHttp\Psr7\StreamWrapper::getResource($response->getBody());	

	$equipableItemSets = Items::fromStream($guzzleStream);
	$allArmorSets = [];
	foreach ($equipableItemSets as $hash => $itemSet) {
		$armorSet = new ArmorSetDefinition($hash, $itemSet);
		$allArmorSets[] = $armorSet;
	}
	try {
		file_put_contents(__DIR__ . '\allArmorSets.json', json_encode($allArmorSets, JSON_THROW_ON_ERROR));
		echo ("All armor sets saved to file!");
		printDivider();
	}
	catch (Exception $e) {
	    echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	// TODO: Test out the viability of reading the big manifest data from a stream instead of downloading the file

	$allWeapons = [];
	$allWeaponPerks = [];
	$allWeaponMods = [];
	$allExoticArmor = [
			EQUIP_BLOCK_HEAD => [
				"Warlock" => [], "Titan" => [], "Hunter" => []
			],
			EQUIP_BLOCK_ARMS => [
				"Warlock" => [], "Titan" => [], "Hunter" => []
			],
			EQUIP_BLOCK_CHEST => [
				"Warlock" => [], "Titan" => [], "Hunter" => []
			],
			EQUIP_BLOCK_LEGS => [
				"Warlock" => [], "Titan" => [], "Hunter" => []
			],
			EQUIP_BLOCK_CLASS => [
				"Warlock" => [], "Titan" => [], "Hunter" => []
			]
		];
	$startTime = new DateTimeImmutable();
	
	// Begin parsing data
	foreach ($itemDefinitions as $hash => $itemDefinition) {

		$categoryHashes = isset($itemDefinition->itemCategoryHashes) ? $itemDefinition->itemCategoryHashes : [];
		
		// WEAPONS
		if (in_array((int)$itemDefinition->inventory->bucketTypeHash, WEAPON_BUCKETS)) {
			if (isDefinitionValidWeapon($itemDefinition)) {
				$weapon = new WeaponDefinition($hash, $itemDefinition);
				$weapon->setEquipmentBucket($itemDefinition->inventory->bucketTypeHash);
				$allWeapons[] = $weapon;
			}
		}

		// WEAPON PERKS
		else if (isDefinitionValidWeaponPerk($itemDefinition)) {
			$weaponPerk = new PerkDefinition($hash, $itemDefinition);
			$allWeaponPerks[] = $weaponPerk;
		}

		// SEASONAL ARTIFACT
		else if (in_array(ITEMCATEGORY_SEASONAL_ARTIFACT, $categoryHashes)) {

			$label = isset($itemDefinition->inventory->stackUniqueLabel) ? $itemDefinition->inventory->stackUniqueLabel : "noLabel";
			if (str_contains($label, ("seasons.season".$currentSeason))) {
				$artifactPerkHashes = array();

				$j = 0;
				foreach ($itemDefinition->preview->derivedItemCategories as $key => $category) {
					if(count($category->items) == 17) {
						// For some reason the perks are in the middle of this list, from indexes 10 to 14
						// Only 5 are available at the start of the season
						$artifactPerkHashes[$j] = array();
						for ($i = 10; $i < 15; $i++) {
							$artifactPerkHashes[$j][] = $category->items[$i]->itemHash;
							// $artifactPerk = new ArtifactPerkDefinition($category->items[$i]->itemHash)
						}
						$j++;
					}
				}
			}
		}

		// ALL MODS
		else if (in_array(ITEMCATEGORY_MODIFICATIONS, $categoryHashes)) {
			// WEAPON MODS
			if (isDefinitionValidWeaponMod($itemDefinition)) {
				$weaponMod = new WeaponModDefinition($hash, $itemDefinition);
				$allWeaponMods[] = $weaponMod;
			}
		}

		// EXOTIC ARMOR
		// we're testing this with tsteps babyyyyyy
		else if (
		 	isset($itemDefinition->collectibleHash) 
		 	&& (int)$itemDefinition->itemType == 2 	// is armor
		 	&& (bool)$itemDefinition->equippable 	// is equippable
		 	&& ((int)$itemDefinition->inventory->tierType ?? MISSING_NUMBER) == 6 // is exotic
		 ) {

			$exotic = new ArmorDefinition($hash, $itemDefinition);

			switch ($exotic->classType) {
				case GuardianClass::Warlock->value:
					$allExoticArmor[$exotic->equipmentSlotTypeHash]["Warlock"][] = $exotic;
					//echo "GuardianClass::Warlock";
					break;
				case GuardianClass::Titan->value:
					$allExoticArmor[$exotic->equipmentSlotTypeHash]["Titan"][] = $exotic;
					//echo "GuardianClass::Titan";
					break;
				case GuardianClass::Hunter->value:
					$allExoticArmor[$exotic->equipmentSlotTypeHash]["Hunter"][] = $exotic;
					//echo "GuardianClass::Hunter";
					break;
				default:
					prettyVar_dump($exotic);
					break;
			}
		}
	}

	// Write to files
	try {
		file_put_contents(__DIR__ . '\allWeapons.json', json_encode($allWeapons, JSON_THROW_ON_ERROR));
		echo ("All weapons saved to file!");
		printDivider();
	}
	catch (Exception $e) {
	    echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	try {
		file_put_contents(__DIR__ . '\allWeaponPerks.json', json_encode($allWeaponPerks, JSON_THROW_ON_ERROR));
		echo ("All weapon perks saved to file!");
		printDivider();
	}
	catch (Exception $e) {
	    echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	try {
		file_put_contents(__DIR__ . '\allWeaponMods.json', json_encode($allWeaponMods, JSON_THROW_ON_ERROR));
		echo ("All weapons mods saved to file!");
		printDivider();
	}
	catch (Exception $e) {
	    echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	try {

		$test = file_put_contents(__DIR__ . '\allExoticArmors.json', json_encode($allExoticArmor, JSON_THROW_ON_ERROR));
		echo ("All exotic armors saved to file!");
		printDivider();
	}
	catch (Exception $e) {
	    echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	$endTime = new DateTimeImmutable();
	$duration = $endTime->diff($startTime);
	echo 'Time expended: ' .  $duration->format("%I:%S");
?>