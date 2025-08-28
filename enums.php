<?php
	enum Bucket : string
	{
	    case Kinetic = 'Kinetic';
	    case Energy = 'Energy';
	    case Power = 'Power';
	    case Whatever = 'Whatever';
	}

	// These correspond to the numbers used in DestinyClassDefinition
	// Except for titan. Titan uses 0 in DestinyInventoryItemDefinition for some reason. Probably just to mess with me.
	enum GuardianClass : int
	{
	    case Hunter = 1;
	    case Warlock = 2;
	    case Titan = 0;
	}

	enum ItemClass : int 
	{
		case Warlock = 21; 	// Warlock class helmets, chest, arms and legs
		case Titan = 22; 	// Titan class helmets, chest, arms and legs
		case Hunter = 23; 	// Hunter class helmets, chest, arms and legs
	}

