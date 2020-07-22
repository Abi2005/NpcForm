<?php


namespace Scarce\NpcForm\Entities;


use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use Scarce\NpcForm\NpcForm;

class Npc extends Human {


    public $form = null;
    public static $sk = null;

    public function __construct(Level $level, CompoundTag $nbt)
    {
        $this->setCanSaveWithChunk(false);
        $this->setSkin(self::$sk);
        parent::__construct($level, $nbt);
    }

    public function initEntity(): void
    {
        $this->propertyManager->setByte(self::DATA_HAS_NPC_COMPONENT, true);
    }

    public function setTitle(string $title){
        $this->setNameTag($title);
    }

    public function setContent(string $text): void {
        $this->propertyManager->setString(self::DATA_INTERACTIVE_TAG, $text);
    }

    //Current Action Of This Is Unknown
    public function setSkinIndex(string $index): void {
        $this->propertyManager->setString(self::DATA_NPC_SKIN_INDEX, $index);
    }

    public function setForm(NpcForm $form):void {
        $this->form = $form;
    }

    public function setActions(?array $data): void {
        $this->propertyManager->setString(self::DATA_NPC_ACTIONS, json_encode($data, JSON_UNESCAPED_UNICODE));
    }


    public function getName(): string {
        return "NPC";
    }

    public static function create(Position $position, int $yaw, int $pitch, ?Skin $skin){
        self::$sk = $skin;
        $nbt = self::createBaseNBT($position, null, $yaw, $pitch);
        $entity = Entity::createEntity("Npc", $position->getLevel(), $nbt);
        $entity->spawnToAll();
    }

    public function handleResponse(Player $player, ?int $data):void {
        if ($this->form !== null){
            if ($this->form instanceof NpcForm){
                $this->form->handleResponse($player, $data);
            }
        }
    }

}
