<?php
/**
 * An helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Http\Models{
/**
 * App\Http\Models\BusinessType
 *
 * @property integer $id
 * @property string $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Business[] $business
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\BusinessType whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\BusinessType whereType($value)
 */
	class BusinessType {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\GiftVoucherValidation
 *
 * @property-read \App\Http\Models\GiftVoucher $voucher
 */
	class GiftVoucherValidation {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\UseTerm
 *
 * @property integer $id
 * @property string $name
 * @property integer $list_order
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\VoucherParameter[] $vouchersParmeters
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UseTerm whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UseTerm whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UseTerm whereListOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UseTerm whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UseTerm whereCreatedAt($value)
 */
	class UseTerm {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\VoucherValidationLog
 *
 * @property integer $id
 * @property integer $voucher_id
 * @property integer $business_id
 * @property integer $user_id
 * @property float $value
 * @property float $balance
 * @property string $log
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Http\Models\Voucher $voucher
 * @property-read \App\Http\Models\Business $business
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherValidationLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherValidationLog whereVoucherId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherValidationLog whereBusinessId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherValidationLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherValidationLog whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherValidationLog whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherValidationLog whereLog($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherValidationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherValidationLog whereUpdatedAt($value)
 */
	class VoucherValidationLog {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\Industry
 *
 * @property integer $id
 * @property string $industry
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Business[] $business
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Industry whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Industry whereIndustry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Industry whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Industry whereUpdatedAt($value)
 */
	class Industry {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\Region
 *
 * @property integer $id
 * @property string $region
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Business[] $business
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Region whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Region whereRegion($value)
 */
	class Region {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\Postcode
 *
 * @property integer $id
 * @property string $postcode
 * @property string $suburb
 * @property string $region
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Business[] $business
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Postcode whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Postcode wherePostcode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Postcode whereSuburb($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Postcode whereRegion($value)
 */
	class Postcode {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\Rule
 *
 * @property integer $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\UserGroup[] $userGroups
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Rule whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Rule whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Rule whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Rule whereUpdatedAt($value)
 */
	class Rule {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\UserGroup
 *
 * @property integer $id
 * @property string $group_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Rule[] $rules
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UserGroup whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UserGroup whereGroupName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UserGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UserGroup whereUpdatedAt($value)
 */
	class UserGroup {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\VoucherImage
 *
 * @property integer $id
 * @property string $name
 * @property string $extension
 * @property string $voucher_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\VoucherParameter[] $voucherParameter
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherImage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherImage whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherImage whereExtension($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherImage whereVoucherType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherImage whereUpdatedAt($value)
 */
	class VoucherImage {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\GiftVoucher
 *
 * @property-read \Customer $customer
 * @property-read \App\Http\Models\GiftVoucherParameter $parameter
 */
	class GiftVoucher {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\City
 *
 * @property integer $id
 * @property string $nz_city
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Business[] $business
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\City whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\City whereNzCity($value)
 */
	class City {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\Order
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $number
 * @property float $tax
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Voucher[] $vouchers
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Order whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Order whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Order whereNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Order whereTax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Order whereUpdatedAt($value)
 */
	class Order {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\BusinessLogo
 *
 * @property integer $id
 * @property integer $business_id
 * @property integer $user_id
 * @property string $name
 * @property string $extension
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Http\Models\Business $business
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\BusinessLogo whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\BusinessLogo whereBusinessId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\BusinessLogo whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\BusinessLogo whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\BusinessLogo whereExtension($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\BusinessLogo whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\BusinessLogo whereUpdatedAt($value)
 */
	class BusinessLogo {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\VoucherParameter
 *
 * @property integer $id
 * @property integer $business_id
 * @property integer $user_id
 * @property integer $voucher_image_id
 * @property string $voucher_type
 * @property string $title
 * @property \Carbon\Carbon $purchase_start
 * @property \Carbon\Carbon $purchase_expiry
 * @property boolean $is_expire
 * @property boolean $is_display
 * @property boolean $is_purchased
 * @property \Carbon\Carbon $valid_from
 * @property integer $valid_for_amount
 * @property string $valid_for_units
 * @property \Carbon\Carbon $valid_until
 * @property boolean $is_limited_quantity
 * @property integer $quantity
 * @property integer $purchased_quantity
 * @property integer $stock_quantity
 * @property string $short_description
 * @property string $long_description
 * @property boolean $is_single_use
 * @property integer $no_of_uses
 * @property float $retail_value
 * @property float $value
 * @property float $min_value
 * @property float $max_value
 * @property boolean $is_valid_during_month
 * @property float $discount_percentage
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Http\Models\Business $business
 * @property-read \App\User $user
 * @property-read \App\Http\Models\VoucherImage $voucherImage
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\UseTerm[] $useTerms
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Voucher[] $vouchers
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereBusinessId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereVoucherImageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereVoucherType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter wherePurchaseStart($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter wherePurchaseExpiry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereIsExpire($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereIsDisplay($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereIsPurchased($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereValidFrom($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereValidForAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereValidForUnits($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereValidUntil($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereIsLimitedQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter wherePurchasedQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereStockQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereShortDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereLongDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereIsSingleUse($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereNoOfUses($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereRetailValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereMinValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereMaxValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereIsValidDuringMonth($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereDiscountPercentage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\VoucherParameter whereUpdatedAt($value)
 */
	class VoucherParameter {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\UserFeedback
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $message
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 * @property-read \App\User $User
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UserFeedback whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UserFeedback whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UserFeedback whereMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UserFeedback whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\UserFeedback whereCreatedAt($value)
 */
	class UserFeedback {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\Business
 *
 * @property integer $id
 * @property integer $logo_id
 * @property integer $city_id
 * @property integer $region_id
 * @property integer $town_id
 * @property integer $postcode_id
 * @property integer $industry_id
 * @property string $facebook_page_id
 * @property boolean $is_active
 * @property string $business_name
 * @property string $trading_name
 * @property string $address1
 * @property string $address2
 * @property string $phone
 * @property string $website
 * @property string $business_email
 * @property string $contact_name
 * @property string $contact_mobile
 * @property boolean $is_featured
 * @property boolean $is_display
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\VoucherParameter[] $voucherParameter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\VoucherValidationLog[] $voucherValidationLogs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\BusinessLogo[] $businessLogos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\BusinessType[] $businessTypes
 * @property-read \App\Http\Models\City $city
 * @property-read \App\Http\Models\Region $region
 * @property-read \App\Http\Models\Town $town
 * @property-read \App\Http\Models\Postcode $postcode
 * @property-read \App\Http\Models\Industry $industry
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereLogoId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereRegionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereTownId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business wherePostcodeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereIndustryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereFacebookPageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereIsActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereBusinessName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereTradingName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereAddress1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereAddress2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereBusinessEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereContactName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereContactMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereIsFeatured($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereIsDisplay($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Business whereDeletedAt($value)
 */
	class Business {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\Town
 *
 * @property integer $id
 * @property string $nz_town
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Business[] $business
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Town whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Town whereNzTown($value)
 */
	class Town {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\Voucher
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $voucher_parameter_id
 * @property integer $order_id
 * @property string $status
 * @property string $code
 * @property float $value
 * @property float $balance
 * @property boolean $is_gift
 * @property boolean $is_instore
 * @property \Carbon\Carbon $delivery_date
 * @property string $recipient_email
 * @property string $message
 * @property \Carbon\Carbon $expiry_date
 * @property integer $validation_times
 * @property \Carbon\Carbon $last_validation_date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\User $user
 * @property-read \App\Http\Models\VoucherParameter $voucherParameter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\VoucherValidationLog[] $voucherValidationLogs
 * @property-read \App\Http\Models\Order $order
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereVoucherParameterId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereBalance($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereIsGift($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereIsInstore($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereDeliveryDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereRecipientEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereExpiryDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereValidationTimes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereLastValidationDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Voucher whereUpdatedAt($value)
 */
	class Voucher {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\GiftVoucherParameter
 *
 * @property-read \App\Http\Models\Merchant $Merchant
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\GiftVoucher[] $GiftVoucher
 */
	class GiftVoucherParameter {}
}

namespace App{
/**
 * App\Lesson
 *
 */
	class Lesson {}
}

namespace App{
/**
 * App\User
 *
 * @property integer $id
 * @property integer $city_id
 * @property integer $region_id
 * @property integer $town_id
 * @property integer $postcode_id
 * @property integer $facebook_user_id
 * @property boolean $is_active
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property string $title
 * @property string $first_name
 * @property string $last_name
 * @property string $gender
 * @property \Carbon\Carbon $dob
 * @property string $address1
 * @property string $address2
 * @property string $phone
 * @property string $mobile
 * @property boolean $is_notify_deal
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property-read \App\Merchant $Merchant
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\FeedBack[] $FeedBack
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\VoucherParameter[] $voucherParameters
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Voucher[] $vouchers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\VoucherValidationLog[] $voucherValidationLogs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\UserGroup[] $userGroups
 * @property-read \App\Http\Models\City $city
 * @property-read \App\Http\Models\Region $region
 * @property-read \App\Http\Models\Town $town
 * @property-read \App\Http\Models\Postcode $postcode
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Business[] $business
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\BusinessLogo[] $businessLogos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Order[] $orders
 * @method static \Illuminate\Database\Query\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRegionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereTownId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePostcodeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFacebookUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereIsActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDob($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAddress1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAddress2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereIsNotifyDeal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDeletedAt($value)
 */
	class User {}
}

