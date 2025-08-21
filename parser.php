<?php
	require __DIR__ . '/vendor/autoload.php';
	use \JsonMachine\Items;

	function prettyVar_Dump($object) {
		echo('<pre>');
		var_dump($object);
		echo('</pre>');
	}
	const MISSING_NUMBER = 999;

	/*
		Category
		Name: INTRINSIC TRAITS
		Hash: 3956125808
		Desc: The base qualities of a weapon.
		Indexes: 0 (aka it is the first socket in the list, same logic follows below)
	*/
	const SOCKET_INTRINSICS = 3956125808;

	/*
		Category
		Name: WEAPON PERKS
		Hash: 4241085061
		Desc: Perks are built in to a given weapon. They can be swapped out an unlimited number of times.
		Indexes: 1, 2, 3, 4, 8 (origin trait), 9 (kill tracker)
	*/
	const SOCKET_ARROWS = 1829212646;
	const SOCKET_BARRELS = 3362409147;
	const SOCKET_BATTERIES = 930927103;
	const SOCKET_BLADES = 193067693;
	const SOCKET_BOLTS = 3571716316;
	const SOCKET_BOWSTRINGS = 3546774010;
	const SOCKET_FRAMES_1 = 1215804697;
	const SOCKET_FRAMES_2 = 1215804696;
	const SOCKET_FRAMES_3 = 1215804699;
	const SOCKET_FRAMES_4 = 2614797986;
	const SOCKET_FRAMES_5 = 3530202021;
	const SOCKET_GRIPS = 514622187;
	const SOCKET_GUARDS = 1218454666;
	const SOCKET_HAFTS = 1048457342;
	const SOCKET_MAGAZINES = 3815406785;
	const SOCKET_MAGAZINES_GL = 3815406785;
	const SOCKET_MAGAZINES_GL_UNIQUE = 4246926293; // for some reason this has another entry lower down
	const SOCKET_RAILS = 1536355127;
	const SOCKET_SCOPES = 1283453667;
	const SOCKET_STOCKS = 2575784089; // for some reason nthis also includes smth called "origins"
	const SOCKET_TUBES = 1656112293;
	const SOCKET_ORIGIN_TRAIT = 3993098925;
	/*
		- these sockets have plugsets (randomizedPlugSetHash found in DestinyPlugSetDefinition json)
		- those plugsets have reusablePlugItems (array of plugItemHash which are all of type DestinyInventoryItemDefinition)
		- the DestinyInventoryItemDefinition version of a perk then has a property called "perks", inside of which is the actual DestinySandboxPerkDefinition
		- that being said you don't need to go that deep cause the description, icon, and name are all in the DestinyInventoryItemDefinition version
	*/

	/*
		Category
		Name: WEAPON COSMETICS
		Hash: 2048875504
		Desc: Attach a cosmetic to a weapon to customize its appearance.
		Indexes: 5
	*/	
	const SOCKET_SHADER = 1288200359;

	/*
		Category
		Name: WEAPON MODS
		Hash: 2685412949
		Desc: Attach a mod to a weapon to improve or add to its perks.
		Indexes: 6 (the actual mod lol), 7 (masterwork), 11 (memento), 12 (deepsight), 13 (level boost)

		Note: 
		Different weapons have different values for the SocketType hash here
		so it's better to use the SocketCategory to find the index of the mod slot you want, and then
		look at reusablePlugItems at that index to get the actual mods available
	*/
	const SOCKET_CATEGORY_WEAPON_MOD = 2685412949;	

	/*
		Category...?
		Name: ???
		Hash: 3583996951
		Desc: ???
		Indexes: 10
		Socket 10 belongs to a category with no name nor description. It might be crafting related.
		Type says: crafting.plugs.frame_identifiers
	*/
	const SOCKET_CRAFTING = 3583996951;


	enum Bucket : string
	{
	    case Kinetic = 'Kinetic';
	    case Energy = 'Energy';
	    case Power = 'Power';
	    case Whatever = 'Whatever';
	}

	class WeaponDefinition
	{
	    public int $hash;
	    public string $name;
	    public string $description;
	    public string $flavorText;
	    public string $icon;
	    public string $iconWatermark;
	    // public $hasIcon = false;
	    // public $isFeaturedItem = false;
    	// public $isHolofoil = false;
    	// public $isAdept = false;
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
	    	// Basics, Icons, and Display Properties
	        $this->hash = (int)$hash;
	        $this->name = $itemDefinition->displayProperties->name ?? '';
			$this->description = $itemDefinition->displayProperties->description ?? '';
			$this->flavorText = $itemDefinition->flavorText ?? '';
			$this->icon = $itemDefinition->displayProperties->icon ?? '';
			$this->iconWatermark = $itemDefinition->iconWatermark ?? '';
			$this->itemTypeDisplayName = $itemDefinition->itemTypeDisplayName ?? '';
			$this->itemTypeAndTierDisplayName = $itemDefinition->itemTypeAndTierDisplayName ?? '';

			// Damage Related Values
			$this->damageTypes = $itemDefinition->damageTypes ?? [];
			$this->defaultDamageType = $itemDefinition->defaultDamageType ?? MISSING_NUMBER;
			$this->defaultDamageTypeHash = $itemDefinition->defaultDamageTypeHash ?? '';
			$this->collectibleHash = $itemDefinition->collectibleHash ?? '';

			// Tier
			$this->tierTypeHash = (int)$itemDefinition->inventory->tierTypeHash;
    		$this->tierTypeName = $itemDefinition->inventory->tierTypeName;
    		$this->tierType = (int)$itemDefinition->inventory->tierType;

    		// Sockets
    		// 4241085061 = Perks socket category hash
    		// 3956125808 = Intrinstics socket category hash
    		// 2685412949 = Mods socket category hash
    		/// TODO: make these all const so there's no magic numbers running around

    		foreach($itemDefinition->sockets->socketCategories as $key => $socket) {
    			// Category Hash -> the array of indexes of the sockets that belong to that category
    			$this->allSocketCategories[(int)$socket->socketCategoryHash] = $socket->socketIndexes;
    		}
    		// Intrinsic Socket
    		// For now I'm gonna assume this is always 0, as in the very first entry
    		// Surely this won't come back to bite me in the ass
    		$this->socketPlugsetHashes["Intrinsic"] = $itemDefinition->sockets->socketEntries[0]->singleInitialItemHash;

    		// Weapon perks sockets
    		foreach ($this->allSocketCategories[4241085061] as $key => $index) {
    			$this->socketPlugsetHashes["Perks"][] = $itemDefinition->sockets->socketEntries[$index]->randomizedPlugSetHash ?? [];
    			// Kill trackers are also included here (usually as the very last index) but the hashes
    			// change and it's annoying to exclude them with conditionals so eh, just slap a ?? there
    		}

    		// Weapon mods sockets
			// foreach ($this->allSocketCategories[2685412949] as $key => $index) {
    		// 	$this->socketPlugsetHashes["Mods"][] = $itemDefinition->sockets->socketEntries[$index]->reusablePlugSetHash ?? [];
    		// 	// The masterwork is also in this category, once again at the last index, but it 
    		// 	// can be ignored cause we can just assume everything is fully MW'd (maybe)
    		// }
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
			
			$this->perkHash = (int)$itemDefinition->perks[0]->perkHash ?? MISSING_NUMBER;
			$this->itemType = $itemDefinition->itemType ?? MISSING_NUMBER;
	    }
	}

	function isDefinitionIsValidWeapon($itemDefinition) {
		return (isset($itemDefinition->collectibleHash) && isset($itemDefinition->defaultDamageTypeHash));
	}

	function isDefinitionIsValidWeaponPerk($itemDefinition) {
		/*
			perhaps: itemTypeDisplayName == Trait (we already check this before)
			and
			itemCategoryHashes = 610365472 (aka "Weapon Mods")
		*/
		return (in_array(610365472, $itemDefinition->itemCategoryHashes));
	}
	function isDefinitionIsValidWeaponMod($itemDefinition) {
		/*
			perhaps: itemTypeDisplayName == Weapon Mod
			and
			itemCategoryHashes = 59 (aka "Mods")
			and
			itemCategoryHashes = 1052191496 (aka "Weapon Mods: Damage")
			and
			itemCategoryHashes = 610365472 (aka "Weapon Mods")
		*/
		return false;
	}
	function isDefinitionIsValidArmorPerk($itemDefinition) {
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


	const BUCKET_WEAPONS_KINETIC = 1498876634;
	const BUCKET_WEAPONS_ENERGY = 2465295065;
	const BUCKET_WEAPONS_POWER = 953998645;
	const BUCKET_MODIFICATIONS = 3313201758;
	const BUCKET_CONSUMABLES = 1469714392; // For some reason perks are in here. Wtf man.

	const WEAPON_BUCKETS = [BUCKET_WEAPONS_KINETIC, BUCKET_WEAPONS_ENERGY, BUCKET_WEAPONS_POWER];

	$latestManifestFileName = "DestinyInventoryItemDefinition-c927add4-d286-4b35-9496-a3a553584307.json";
	$itemDefinitions = Items::fromFile('C:\Users\incomescrane\Desktop\\' . $latestManifestFileName);

	$allWeapons = array();
	$startTime = new DateTimeImmutable();
	foreach ($itemDefinitions as $hash => $itemDefinition) {
		if (in_array((int)$itemDefinition->inventory->bucketTypeHash, WEAPON_BUCKETS)) {
			if (isDefinitionIsValidWeapon($itemDefinition)) {
				$weapon = new WeaponDefinition($hash, $itemDefinition);
				$weapon->setEquipmentBucket($itemDefinition->inventory->bucketTypeHash);
				prettyVar_Dump($weapon);
				$allWeapons[] = $weapon;
			}
		}

		// TODO: clean up these checks
		// else if ((int)$itemDefinition->inventory->bucketTypeHash == BUCKET_CONSUMABLES
		// 	&& ($itemDefinition->itemTypeDisplayName ?? "") == "Trait") {
		// 	if (isDefinitionIsValidWeaponPerk($itemDefinition)) {
		// 		$weaponPerk = new PerkDefinition($hash, $itemDefinition);
		// 		prettyVar_Dump($weaponPerk);
		// 	}
		// }
	}
	try {
		file_put_contents('allWeapons.json', json_encode($allWeapons, JSON_THROW_ON_ERROR));
	}
	catch (Exception $e) {
	    echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	$endTime = new DateTimeImmutable();
	$duration = $endTime->diff($startTime);
	echo $duration->format("%I:%S");
?>