<?php

namespace App\Form;

use Symfony\Component\Form\FormFactoryInterface;

/**
 * AppFormFactory class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class AppFormFactory implements AppFormFactoryInterface
{
    /**
     * Stores FormFactoryInterface
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $name, $object)
    {
        switch ($name) {
            case 'address-create':
            case 'address-modify':
                $form = AddressType::class;
                break;
            case 'blog-create':
            case 'blog-modify':
                $form = BlogType::class;
                break;
            case 'category-create':
            case 'category-modify':
                $form = CategoryType::class;
                break;
            case 'child-create':
            case 'child-modify':
                $form = ChildType::class;
                break;
            case 'child-presence-create':
            case 'child-presence-modify':
                $form = ChildPresenceType::class;
                break;
            case 'component-create':
            case 'component-modify':
                $form = ComponentType::class;
                break;
            case 'family-create':
            case 'family-modify':
                $form = FamilyType::class;
                break;
            case 'food-create':
            case 'food-modify':
                $form = FoodType::class;
                break;
            case 'group-activity-create':
            case 'group-activity-modify':
                $form = GroupActivityType::class;
                break;
            case 'invoice-create':
            case 'invoice-modify':
                $form = InvoiceType::class;
                break;
            case 'invoice-product-create':
                $form = InvoiceProductType::class;
                break;
            case 'invoice-component-create':
                $form = InvoiceComponentType::class;
                break;
            case 'location-create':
            case 'location-modify':
                $form = LocationType::class;
                break;
            case 'mail-create':
            case 'mail-modify':
                $form = MailType::class;
                break;
            case 'meal-create':
            case 'meal-modify':
                $form = MealType::class;
                break;
            case 'person-create':
            case 'person-modify':
                $form = PersonType::class;
                break;
            case 'phone-create':
            case 'phone-modify':
                $form = PhoneType::class;
                break;
            case 'pickup-create':
            case 'pickup-modify':
                $form = PickupType::class;
                break;
            case 'pickup-activity-create':
            case 'pickup-activity-modify':
                $form = PickupActivityType::class;
                break;
            case 'product-create':
            case 'product-modify':
                $form = ProductType::class;
                break;
            case 'product-cancelled-date-create':
            case 'product-cancelled-date-modify':
                $form = ProductCancelledDateType::class;
                break;
            case 'registration-create':
            case 'registration-modify':
                $form = RegistrationType::class;
                break;
            case 'ride-create':
            case 'ride-modify':
                $form = RideType::class;
                break;
            case 'school-create':
            case 'school-modify':
                $form = SchoolType::class;
                break;
            case 'season-create':
            case 'season-modify':
                $form = SeasonType::class;
                break;
            case 'sport-create':
            case 'sport-modify':
                $form = SportType::class;
                break;
            case 'staff-create':
            case 'staff-modify':
                $form = StaffType::class;
                break;
            case 'staff-presence-create':
            case 'staff-presence-modify':
                $form = StaffPresenceType::class;
                break;
            case 'television-create':
            case 'television-modify':
                $form = TelevisionType::class;
                break;
            case 'transaction-create':
                $form = TransactionType::class;
                break;
            case 'transaction-modify':
                $form = TransactionModifyType::class;
                break;
            case 'vehicle-create':
            case 'vehicle-modify':
                $form = VehicleType::class;
                break;
            case 'week-create':
            case 'week-modify':
                $form = WeekType::class;
                break;
            case 'historic-sms-create':
            case 'historic-sms-modify':
                    $form = HistoricSmsType::class;
                    break;
            default:
                $form = null;
                break;
        }

        if (null !== $form) {
            return $this->formFactory->create($form, $object, ['csrf_protection' => false]);
        }

        return false;
    }
}
