<?php 

namespace Meli;

/**
 * MeliFakeProvider
 */
class MeliFakeProvider extends \Faker\Provider\Base
{
    /**
     * @var array of shipping_modes
     */
    public static $shipping_modes = ['me1', 'me2', 'custom', 'not_specified'];

    /**
     * @var array of shipping_options
     */
    public static $shipping_options = ['custom'];

    /**
     * @var array of supported countries by ML
     */
    public static $supported_countries = ['MLA', 'MLB', 'MCO', 'MCR', 'MEC', 'MLC', 'MLM', 'MLU', 'MLV', 'MPA', 'MPE', 'MPT', 'MRD'];

    /**
     * @var array of categories
     */
    public static $categories = [
        'Acessórios para Veículos', 'Agro, Indústria e Comércio', 'Alimentos e Bebidas', 'Animais', 'Arte e Artesanato', 'Bebês', 'Beleza e Cuidado Pessoal',
        'Brinquedos e Hobbies', 'Calçados, Roupas e Bolsas', 'Câmeras e Acessórios', 'Carros, Motos e Outros', 'Casa, Móveis e Decoração', 'Celulares e Telefones',
        'Coleções e Comics', 'Eletrodomésticos', 'Eletrônicos, Áudio e Vídeo', 'Esportes e Fitness', 'Ferramentas e Construção', 'Filmes e Seriados', 'Games',
        'Imóveis', 'Informática', 'Ingressos', 'Instrumentos Musicais', 'Joias e Relógios', 'Livros', 'Música', 'Saúde', 'Serviços', 'Adultos', 'Esoterismo e Ocultismo', 'Materiais Escolares', 'Moedas Virtuais', 'Tabacaria', 'Outros'
    ];

    /**
     * @var array of buying_modes
     */
    public static $buying_modes = ['buy_it_now', 'auction'];

    /**
     * @var array of currencies
     */
    public static $currencies = ['BRL'];

    /**
     * @var array of item_conditions
     */
    public static $item_conditions = ['used', 'not_specified', 'new'];

    /**
    * @example 'me1'
    * 
    * @param bool $single if must return one or more
    */
    public static function shipping_mode($single = true)
    {
        return $single ? static::randomElement(static::$shipping_modes) : static::randomElements(static::$shipping_modes, parent::numberBetween(1, 4), true);
    }

    /**
    * @example 'MLB41234' 
    */
    public static function id()
    {
        return static::randomElement(static::$supported_countries).parent::randomNumber(4, true);
    }

    /**
    * @example 'MLB' 
    */
    public static function country()
    {
        return static::randomElement(static::$supported_countries);
    }

    /**
    * @example 'Materiais Escolares' 
    */
    public static function category()
    {
        return static::randomElement(static::$categories);
    }

    /**
    * @example 'buy_it_now' 
    * 
    * @param bool $single if must return one or more
    */
    public static function buying_mode($single = true)
    {
        return $single ? static::randomElement(static::$buying_modes) : static::randomElements(static::$buying_modes, parent::numberBetween(1, 4), true);
    }

    /**
    * @example 'BRL' 
    * 
    * @param bool $single if must return one or more
    */
    public static function currency($single = true)
    {
        return $single ? static::randomElement(static::$currencies) : static::randomElements(static::$currencies, parent::numberBetween(1, 4), true);
    }

    /**
    * @example 'used' 
    * 
    * @param bool $single if must return one or more
    */
    public static function item_condition($single = true)
    {
        return $single ? static::randomElement(static::$item_conditions) : static::randomElements(static::$item_conditions, parent::numberBetween(1, 4), true);
    }

    /**
    * @example 'custom' 
    * 
    * @param bool $single if must return one or more
    */
    public static function shipping_option($single = true)
    {
        return $single ? static::randomElement(static::$shipping_options) : static::randomElements(static::$shipping_options, parent::numberBetween(1, 4), true);
    }
}