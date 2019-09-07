<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute 必须被接受。',
    'active_url'           => ':attribute 不是一个有效的URL。',
    'after'                => ':attribute 必须是一个在 :date 之后的日期。',
    'after_or_equal'       => ':attribute 必须是一个在 :date 之后或者是同一天的日期。',
    'alpha'                => ':attribute 只能包含字母。',
    'alpha_dash'           => ':attribute 只能包含字母，数字和波折号。',
    'alpha_num'            => ':attribute 只能包含字母和数字。',
    'array'                => ':attribute 必须是一个数组。',
    'before'               => ':attribute 必须是一个在 :date 之前的日期。',
    'before_or_equal'      => ':attribute 必须是一个在 :date 之前或者同一天的日期。',
    'between'              => [
        'numeric' => ':attribute 必须介于 :min 和 :max 之间。',
        'file'    => ':attribute 必须介于:min kilobytes 和 :max kilobytes 之间。',
        'string'  => ':attribute 必须介于 :min 到 :max 个字符。',
        'array'   => ':attribute 必须拥有 :min 到 :max 个元素。',
    ],
    'boolean'              => ':attribute 只能为true或者false。',
    'confirmed'            => ':attribute 与确认值不符。',
    'date'                 => ':attribute 不是一个有效的日期。',
    'date_format'          => ':attribute 与有效日期格式不符 :format。',
    'different'            => ':attribute 和 :other 必须不同。',
    'digits'               => ':attribute 必须是 :digits 位。',
    'digits_between'       => ':attribute 必须介于 :min 和 :max 位之间。',
    'dimensions'           => ':attribute 具有无效的图像尺寸。',
    'distinct'             => ':attribute 字段具有重复值。',
    'email'                => ':attribute 必须是一个有效的E-mail地址。',
    'exists'               => '所选 :attribute 是无效的。',
    'file'                 => ':attribute 必须是一个文件。',
    'filled'               => ':attribute 必须有一个值。',
    'image'                => ':attribute 必须是图片格式。',
    'in'                   => '所选 :attribute 是无效的。',
    'in_array'             => ':attribute 字段在 :other 中不存在。',
    'integer'              => ':attribute 必须是整数。',
    'ip'                   => ':attribute 必须是一个有效的IP地址。',
    'ipv4'                 => ':attribute 必须是一个有效的IPv4地址。',
    'ipv6'                 => ':attribute 必须是一个有效的IPv6地址。',
    'json'                 => ':attribute 必须是一个有效的JSON字符串。',
    'max'                  => [
        'numeric' => ':attribute 不能超过 :max 。',
        'file'    => ':attribute 不能超过 :max kilobytes。',
        'string'  => ':attribute 不能超过 :max 个字符。',
        'array'   => ':attribute 不能超过 :max 个元素。',
    ],
    'mimes'                => ':attribute 必须是一个 :values 类型的文件。',
    'mimetypes'            => ':attribute 必须是一个 :values 类型的文件。',
    'min'                  => [
        'numeric' => ':attribute 必须至少为 :min 。',
        'file'    => ':attribute 必须至少为 :min kilobytes。',
        'string'  => ':attribute 必须至少为 :min 个字符。',
        'array'   => ':attribute 必须至少有 :min 个元素。',
    ],
    'not_in'               => '所选 :attribute 是无效的。',
    'numeric'              => ':attribute 必须是个数字。',
    'present'              => ':attribute 字段必须存在。',
    'regex'                => ':attribute 格式是无效的。',
    'required'             => ':attribute 字段是必须的。',
    'required_if'          => '当 :other 是 :value 时，:attribute 字段是必须的。',
    'required_unless'      => '当 :other 在 :values 时，:attribute 字段是必须的。',
    'required_with'        => '当 :value 存在时，:attribute 字段是必须的。',
    'required_with_all'    => '当 :value 存在时，:attribute 字段是必须的。',
    'required_without'     => '当 :value 不存在时，:attribute 字段是必须的。',
    'required_without_all' => '当 :value 的值都不存在时，:attribute 字段是必须的。',
    'same'                 => ':attribute 和 :other 必须匹配。',
    'size'                 => [
        'numeric' => ':attribute 大小必须为 :size。',
        'file'    => ':attribute 大小必须为 :size kilobytes。',
        'string'  => ':attribute 大小必须为 :size 个字符.。',
        'array'   => ':attribute 必须包含 :size 个元素。',
    ],
    'string'               => ':attribute 必须是个字符串。',
    'timezone'             => ':attribute 必须是一个有效的地区。',
    'unique'               => ':attribute 已经被使用。',
    'uploaded'             => '上传 :attribute 失败。',
    'url'                  => ':attribute 格式是无效的。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => '自定义消息',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
