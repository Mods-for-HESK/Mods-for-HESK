<?php

namespace BusinessObjects;

class Category {
    /**
     * @var int The Categories ID
     */
    public $id;

    /**
     * @var int Categories order number
     */
    public $catOrder;

    /**
     * @var bool Tickets autoassigned in this Categories
     */
    public $autoAssign;

    /**
     * @var int The type of Categories (1 = Private, 2 = Public)
     */
    public $type;

    /**
     * @var int The Categories's usage (0 = Tickets and Events, 1 = Tickets, 2 = Events)
     */
    public $usage;

    /**
     * @var string? The color of the Categories
     */
    public $color;

    /**
     * @var int The default Tickets priority
     */
    public $priority;

    /**
     * @var int|null The manager for the Categories, if applicable
     */
    public $manager;

    /**
     * @var bool Indication if the user has access to the Categories
     */
    public $accessible;
}