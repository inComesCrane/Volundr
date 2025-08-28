<?php
	class WeaponDefinition
	{
	    public int $hash;
	    public string $name;
	    public string $description;
	    public string $flavorText;
	    public string $icon;
	    public string $iconWatermark;
	    public string $iconWatermarkFeatured;
	    // public $hasIcon = false;
	    public bool $isFeaturedItem;
    	public bool $isHolofoil;
    	public bool $isAdept;
    	public string $itemTypeDisplayName;
    	public string $itemTypeAndTierDisplayName;

    	public int $tierTypeHash;
    	public string $tierTypeName;
    	public int $tierType; // I think this is some sort of Enum?

    	public $socketPlugsetHashes = [];
    	public $allSocketCategories = [];
    	// oh no
    	// public $stats = {};

    	public $equippingBlock_equipmentSlotTypeHash = 1498876634;
    	public $equippingBlock_ammoType = 1498876634;
    	public Bucket $equipmentBucket;

    	/// TODO: Figure out if this even needs to be stored
    	public $damageTypes = [];
    	public int $defaultDamageType;
    	public $defaultDamageTypeHash;
    	public $collectibleHash;

	    public function __construct($hash, $itemDefinition) {
	    	// Display Properties
	        $this->hash = (int)$hash;
	        $this->name = $itemDefinition->displayProperties->name ?? '';
			$this->description = $itemDefinition->displayProperties->description ?? '';
			$this->flavorText = $itemDefinition->flavorText ?? '';
			
			// Icon
			$this->icon = $itemDefinition->displayProperties->icon ?? '';
			$this->iconWatermark = $itemDefinition->iconWatermark ?? '';
			$this->iconWatermarkFeatured = $itemDefinition->iconWatermarkFeatured ?? '';

			// Booleans
			$this->isFeaturedItem = (bool)$itemDefinition->isFeaturedItem;
			$this->isHolofoil = (bool)$itemDefinition->isHolofoil;
			$this->isAdept = (bool)$itemDefinition->isAdept;

			// Type
			$this->itemTypeDisplayName = $itemDefinition->itemTypeDisplayName ?? '';
			$this->itemTypeAndTierDisplayName = $itemDefinition->itemTypeAndTierDisplayName ?? '';

			// Tier
			$this->tierTypeHash = (int)$itemDefinition->inventory->tierTypeHash;
    		$this->tierTypeName = $itemDefinition->inventory->tierTypeName;
    		$this->tierType = (int)$itemDefinition->inventory->tierType;

			// Damage Related Values
			$this->damageTypes = $itemDefinition->damageTypes ?? [];
			$this->defaultDamageType = $itemDefinition->defaultDamageType ?? MISSING_NUMBER;
			$this->defaultDamageTypeHash = $itemDefinition->defaultDamageTypeHash ?? '';
			$this->collectibleHash = $itemDefinition->collectibleHash ?? '';

    		// Sockets
    		// 4241085061 = Perks socket category hash
    		// 3956125808 = Intrinstics socket category hash
    		// 2685412949 = Mods socket category hash
    		/// TODO: make these all const so there's no magic numbers running around

			// Find the indexes of entries which have perk info from the categories
    		foreach($itemDefinition->sockets->socketCategories as $key => $socket) {
    			// Category Hash => the array of indexes of the sockets that belong to that category
    			$this->allSocketCategories[(int)$socket->socketCategoryHash] = $socket->socketIndexes;
    		}
    		// Intrinsic Socket
    		// For now I'm gonna assume this is always 0, aka the very first entry
    		// Surely this won't come back to bite me in the ass
    		$this->socketPlugsetHashes["Intrinsic"] = $itemDefinition->sockets->socketEntries[0]->singleInitialItemHash;

    		// Weapon perks sockets
    		/*
				This conditional only exists to deal with the white version of Khvostov 7G-02 (hash: 1619016919)
				It only has an intrisic category which is already saved by the lines above

				Kill trackers are also included here (usually as the very last index) but the hashes
				change and it's annoying to exclude them with conditionals so eh, just slap a ?? there
    		*/
    		if (isset($this->allSocketCategories[4241085061])) {
	    		foreach ($this->allSocketCategories[4241085061] as $key => $index) {
	    			$this->socketPlugsetHashes["Perks"][] = $itemDefinition->sockets->socketEntries[$index]->randomizedPlugSetHash ?? [];
	    		}
    		}

    		// Weapon mods sockets
    		/*
				Items of Rare and lower rarity do not have mod sockets.

				The masterwork is also in this category, once again at the last index, but it 
	    		can be ignored cause we can just assume everything is fully MW'd (probably)
    		*/
    		if (isset($this->allSocketCategories[4241085061])) {
				foreach ($this->allSocketCategories[2685412949] as $key => $index) {

	    			$this->socketPlugsetHashes["Mods"][] = $itemDefinition->sockets->socketEntries[$index]->reusablePlugSetHash ?? [];
	    		}
	    	}
	    }

	    public function setEquipmentBucket(int $bucket) {
	    	switch($bucket) {
	    		case BUCKET_WEAPONS_KINETIC:
	    			$this->equipmentBucket = Bucket::Kinetic;
	    			break;
	    		case BUCKET_WEAPONS_ENERGY:
	    			$this->equipmentBucket = Bucket::Energy;
	    			break;
	    		case BUCKET_WEAPONS_POWER:
	    			$this->equipmentBucket = Bucket::Power;
	    			break;
	    		default:
	    			$this->equipmentBucket = Bucket::Whatever;
	    	}
	    }
	}

	class PerkDefinition {
	    public int $hash;
	    public string $name;
	    public string $description;
	    public string $icon;
    	public string $itemTypeDisplayName;
    	public string $itemTypeAndTierDisplayName;

    	public int $plugCategoryHash;
    	public int $plugCategoryIdentifier;

    	public int $perkHash;
    	public int $itemType;


	    public function __construct($hash, $itemDefinition) {
	    	// Basics, Icons, and Display Properties
	        $this->hash = (int)$hash;
	        $this->name = $itemDefinition->displayProperties->name ?? '';
			$this->description = $itemDefinition->displayProperties->description ?? '';
			$this->icon = $itemDefinition->displayProperties->icon ?? '';
			$this->itemTypeDisplayName = $itemDefinition->itemTypeDisplayName ?? '';
			$this->itemTypeAndTierDisplayName = $itemDefinition->itemTypeAndTierDisplayName ?? '';

			$this->plugCategoryHash = (int)$itemDefinition->plug->plugCategoryHash ?? MISSING_NUMBER;
			$this->plugCategoryIdentifier = (int)$itemDefinition->plug->plugCategoryIdentifier ?? MISSING_NUMBER;
			
			/* 
				Perks should only be missing for a small number of exotics, both of which (at the moment)
				happen to be pre-order exotics: Quicksilver Storm and No Land Beyond
				Interesting to note this same issue does not extend to others like Tesselation or NTTE
				TODO: look into why this is, for curiosity's sake
			*/ 
			$this->perkHash = isset($itemDefinition->perks[0]) ? (int)$itemDefinition->perks[0]->perkHash : MISSING_NUMBER;			
			$this->itemType = $itemDefinition->itemType ?? MISSING_NUMBER;
	    }
	}

	class WeaponModDefinition {
	    public int $hash;
	    public string $name;
	    public string $description;
	    public string $icon;
    	public string $itemTypeDisplayName;
    	public string $itemTypeAndTierDisplayName;

    	public int $plugCategoryHash;
    	public string $plugCategoryIdentifier;

    	public int $perkHash;
    	public int $itemType;


	    public function __construct($hash, $itemDefinition) {
	    	// Basics, Icons, and Display Properties
	        $this->hash = (int)$hash;
	        $this->name = $itemDefinition->displayProperties->name ?? '';
			$this->description = $itemDefinition->displayProperties->description ?? '';
			$this->icon = $itemDefinition->displayProperties->icon ?? '';
			$this->itemTypeDisplayName = $itemDefinition->itemTypeDisplayName ?? '';
			$this->itemTypeAndTierDisplayName = $itemDefinition->itemTypeAndTierDisplayName ?? '';

			$this->plugCategoryHash = (int)$itemDefinition->plug->plugCategoryHash ?? MISSING_NUMBER;
			$this->plugCategoryIdentifier = $itemDefinition->plug->plugCategoryIdentifier ?? "";
			
			$this->perkHash = (int)$itemDefinition->perks[0]->perkHash ?? MISSING_NUMBER;
			$this->itemType = $itemDefinition->itemType ?? MISSING_NUMBER;
	    }
	}

	class ArtifactPerkDefinition {
	    public int $hash;
	    public string $name;
	    public string $description;
	    public string $icon;
    	public string $itemTypeDisplayName;
    	public string $itemTypeAndTierDisplayName;

    	public int $plugCategoryHash;
    	public string $plugCategoryIdentifier;

    	public int $perkHash;
    	public int $itemType;


	    public function __construct($hash, $itemDefinition) {
	    	// Basics, Icons, and Display Properties
	        $this->hash = (int)$hash;
	        $this->name = $itemDefinition->displayProperties->name ?? '';
			$this->description = $itemDefinition->displayProperties->description ?? '';
			$this->icon = $itemDefinition->displayProperties->icon ?? '';
			$this->itemTypeDisplayName = $itemDefinition->itemTypeDisplayName ?? '';
			$this->itemTypeAndTierDisplayName = $itemDefinition->itemTypeAndTierDisplayName ?? '';

			$this->plugCategoryHash = (int)$itemDefinition->plug->plugCategoryHash ?? MISSING_NUMBER;
			$this->plugCategoryIdentifier = $itemDefinition->plug->plugCategoryIdentifier ?? "";
			
			$this->perkHash = (int)$itemDefinition->perks[0]->perkHash ?? MISSING_NUMBER;
			$this->itemType = $itemDefinition->itemType ?? MISSING_NUMBER;
	    }
	}

	class ArmorSetDefinition {
	    public int $hash;
	    public string $name;
	    public string $description;

	    public $perks = [];

	    public function __construct($hash, $itemDefinition) {
	    	// Basics and Display Properties
	        $this->hash = (int)$hash;
	        $this->name = $itemDefinition->displayProperties->name ?? '';
			$this->description = $itemDefinition->displayProperties->description ?? '';
			$this->perks = $itemDefinition->setPerks;
	    }
	}

	class ArmorDefinition {
	    public int $hash;
	    public string $name;
	    public string $description;
	    public string $flavorText;
	    public string $icon;
	    public string $iconWatermark;
	    public string $iconWatermarkFeatured;
	    public bool $isFeaturedItem;

	    // Tier
		public int $tierTypeHash;
    	public string $tierTypeName;
    	public int $tierType; 		// enum TierType

    	// Equipment Slot and Class restriction
    	public int $equipmentSlotTypeHash;
    	//public $itemCategoryHashes;
    	public int $itemType; 		// enum DestinyItemType
    	public int $itemSubtype; 	// enum DestinyItemSubType
    	public int $classType; 		// enum DestinyClass
    	private string $classAsString;

	    public $perks = [];

	    public function __construct($hash, $itemDefinition) {
	    	// Display Properties
	        $this->hash = (int)$hash;
	        $this->name = $itemDefinition->displayProperties->name ?? '';
			$this->description = $itemDefinition->displayProperties->description ?? '';
			$this->flavorText = $itemDefinition->flavorText ?? '';
			
			// Icon
			$this->icon = $itemDefinition->displayProperties->icon ?? '';
			$this->iconWatermark = $itemDefinition->iconWatermark ?? '';
			$this->iconWatermarkFeatured = $itemDefinition->iconWatermarkFeatured ?? '';
			
			// Featured
			$this->isFeaturedItem = (bool)$itemDefinition->isFeaturedItem;

			// Type
			$this->itemTypeDisplayName = $itemDefinition->itemTypeDisplayName ?? '';
			$this->itemTypeAndTierDisplayName = $itemDefinition->itemTypeAndTierDisplayName ?? '';

			// Tier
			$this->tierTypeHash = (int)$itemDefinition->inventory->tierTypeHash;
    		$this->tierTypeName = $itemDefinition->inventory->tierTypeName;
    		$this->tierType = (int)$itemDefinition->inventory->tierType;

    		// Equipment Slot and Class restriction
    		$this->equipmentSlotTypeHash = $itemDefinition->equippingBlock->equipmentSlotTypeHash ?? MISSING_NUMBER;
    		//$this->itemCategoryHashes = $itemDefinition->itemCategoryHashes ?? [];
    		$this->itemType = $itemDefinition->itemType ?? MISSING_NUMBER; 
    		$this->itemSubType = $itemDefinition->itemSubType ?? MISSING_NUMBER; 
    		$this->classType = $itemDefinition->classType ?? MISSING_NUMBER; 

    		// Socket Categories and Perks
    		/*
    			TODO: make these all const so there's no magic numbers running around
    			Only need the perks category here, thankfully

				590099826 = Armor Mods socket category hash
				1926152773 = Armor Cosmetics socket category hash
				3154740035 = Armor Perks socket category hash
    		*/

			// Find the indexes of entries which could have perk info from the categories
    		$perkIndexes = array_filter($itemDefinition->sockets->socketCategories, function ($socket) {
    			// This is the category that has all our perk indexes
    			return $socket->socketCategoryHash == 3154740035
    				|| $socket->socketCategoryHash == 2518356196; // this hash is only used on the exotic class items
    		});
    		$perkIndexes = array_values($perkIndexes); // reindex

    		if (isset($perkIndexes[1])) { // has more than one perk, like the class items
    			$perkIndexes = array_merge($perkIndexes[0]->socketIndexes, $perkIndexes[1]->socketIndexes);
    		}
    		else {
    			$perkIndexes = $perkIndexes[0]->socketIndexes;
    		}

    		foreach($perkIndexes as $key => $index) {
    			if (!empty($itemDefinition->sockets->socketEntries[$index]->singleInitialItemHash)) {
    				$this->perks[] = $itemDefinition->sockets->socketEntries[$index]->singleInitialItemHash;
    			}
    		}
	    }
	}