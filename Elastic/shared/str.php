<?php
/**
 * @author : 김중권
 * @date   : 2020-12-17
 *
 * 엘라스틱서치 관련 문자열 핸들러(오토로드시 클래스파일명 매핑 위해 가져옴)
 */
class Elastic_shared_str
{
    /**    Constants                */
    /**    Regular Expressions        */
    //    Fraction
    const STRING_REGEXP_FRACTION    = '(-?)(\d+)\s+(\d+\/\d+)';

    /**
     * Control characters array
     *
     * @var string[]
     */
    private static $controlCharacters = array();

    /**
     * SYLK Characters array
     *
     * $var array
     */
    private static $SYLKCharacters = array();

    /**
     * Decimal separator
     *
     * @var string
     */
    private static $decimalSeparator;

    /**
     * Thousands separator
     *
     * @var string
     */
    private static $thousandsSeparator;

    /**
     * Currency code
     *
     * @var string
     */
    private static $currencyCode;

    /**
     * Is mbstring extension avalable?
     *
     * @var boolean
     */
    private static $isMbstringEnabled;

    /**
     * Is iconv extension avalable?
     *
     * @var boolean
     */
    private static $isIconvEnabled;

    /**
     * Build control characters array
     */
    private static function buildControlCharacters()
    {
        for ($i = 0; $i <= 31; ++$i) {
            if ($i != 9 && $i != 10 && $i != 13) {
                $find = '_x' . sprintf('%04s', strtoupper(dechex($i))) . '_';
                $replace = chr($i);
                self::$controlCharacters[$find] = $replace;
            }
        }
    }

    public static function buildCharacterSets()
    {
        if (empty(self::$controlCharacters)) {
            self::buildControlCharacters();
        }
        if (empty(self::$SYLKCharacters)) {
            self::buildSYLKCharacters();
        }
    }
    /**
     * Build SYLK characters array
     */
    private static function buildSYLKCharacters()
    {
        self::$SYLKCharacters = array(
            "\x1B 0"  => chr(0),
            "\x1B 1"  => chr(1),
            "\x1B 2"  => chr(2),
            "\x1B 3"  => chr(3),
            "\x1B 4"  => chr(4),
            "\x1B 5"  => chr(5),
            "\x1B 6"  => chr(6),
            "\x1B 7"  => chr(7),
            "\x1B 8"  => chr(8),
            "\x1B 9"  => chr(9),
            "\x1B :"  => chr(10),
            "\x1B ;"  => chr(11),
            "\x1B <"  => chr(12),
            "\x1B :"  => chr(13),
            "\x1B >"  => chr(14),
            "\x1B ?"  => chr(15),
            "\x1B!0"  => chr(16),
            "\x1B!1"  => chr(17),
            "\x1B!2"  => chr(18),
            "\x1B!3"  => chr(19),
            "\x1B!4"  => chr(20),
            "\x1B!5"  => chr(21),
            "\x1B!6"  => chr(22),
            "\x1B!7"  => chr(23),
            "\x1B!8"  => chr(24),
            "\x1B!9"  => chr(25),
            "\x1B!:"  => chr(26),
            "\x1B!;"  => chr(27),
            "\x1B!<"  => chr(28),
            "\x1B!="  => chr(29),
            "\x1B!>"  => chr(30),
            "\x1B!?"  => chr(31),
            "\x1B'?"  => chr(127),
            "\x1B(0"  => '€', // 128 in CP1252
            "\x1B(2"  => '‚', // 130 in CP1252
            "\x1B(3"  => 'ƒ', // 131 in CP1252
            "\x1B(4"  => '„', // 132 in CP1252
            "\x1B(5"  => '…', // 133 in CP1252
            "\x1B(6"  => '†', // 134 in CP1252
            "\x1B(7"  => '‡', // 135 in CP1252
            "\x1B(8"  => 'ˆ', // 136 in CP1252
            "\x1B(9"  => '‰', // 137 in CP1252
            "\x1B(:"  => 'Š', // 138 in CP1252
            "\x1B(;"  => '‹', // 139 in CP1252
            "\x1BNj"  => 'Œ', // 140 in CP1252
            "\x1B(>"  => 'Ž', // 142 in CP1252
            "\x1B)1"  => '‘', // 145 in CP1252
            "\x1B)2"  => '’', // 146 in CP1252
            "\x1B)3"  => '“', // 147 in CP1252
            "\x1B)4"  => '”', // 148 in CP1252
            "\x1B)5"  => '•', // 149 in CP1252
            "\x1B)6"  => '–', // 150 in CP1252
            "\x1B)7"  => '—', // 151 in CP1252
            "\x1B)8"  => '˜', // 152 in CP1252
            "\x1B)9"  => '™', // 153 in CP1252
            "\x1B):"  => 'š', // 154 in CP1252
            "\x1B);"  => '›', // 155 in CP1252
            "\x1BNz"  => 'œ', // 156 in CP1252
            "\x1B)>"  => 'ž', // 158 in CP1252
            "\x1B)?"  => 'Ÿ', // 159 in CP1252
            "\x1B*0"  => ' ', // 160 in CP1252
            "\x1BN!"  => '¡', // 161 in CP1252
            "\x1BN\"" => '¢', // 162 in CP1252
            "\x1BN#"  => '£', // 163 in CP1252
            "\x1BN("  => '¤', // 164 in CP1252
            "\x1BN%"  => '¥', // 165 in CP1252
            "\x1B*6"  => '¦', // 166 in CP1252
            "\x1BN'"  => '§', // 167 in CP1252
            "\x1BNH " => '¨', // 168 in CP1252
            "\x1BNS"  => '©', // 169 in CP1252
            "\x1BNc"  => 'ª', // 170 in CP1252
            "\x1BN+"  => '«', // 171 in CP1252
            "\x1B*<"  => '¬', // 172 in CP1252
            "\x1B*="  => '­', // 173 in CP1252
            "\x1BNR"  => '®', // 174 in CP1252
            "\x1B*?"  => '¯', // 175 in CP1252
            "\x1BN0"  => '°', // 176 in CP1252
            "\x1BN1"  => '±', // 177 in CP1252
            "\x1BN2"  => '²', // 178 in CP1252
            "\x1BN3"  => '³', // 179 in CP1252
            "\x1BNB " => '´', // 180 in CP1252
            "\x1BN5"  => 'µ', // 181 in CP1252
            "\x1BN6"  => '¶', // 182 in CP1252
            "\x1BN7"  => '·', // 183 in CP1252
            "\x1B+8"  => '¸', // 184 in CP1252
            "\x1BNQ"  => '¹', // 185 in CP1252
            "\x1BNk"  => 'º', // 186 in CP1252
            "\x1BN;"  => '»', // 187 in CP1252
            "\x1BN<"  => '¼', // 188 in CP1252
            "\x1BN="  => '½', // 189 in CP1252
            "\x1BN>"  => '¾', // 190 in CP1252
            "\x1BN?"  => '¿', // 191 in CP1252
            "\x1BNAA" => 'À', // 192 in CP1252
            "\x1BNBA" => 'Á', // 193 in CP1252
            "\x1BNCA" => 'Â', // 194 in CP1252
            "\x1BNDA" => 'Ã', // 195 in CP1252
            "\x1BNHA" => 'Ä', // 196 in CP1252
            "\x1BNJA" => 'Å', // 197 in CP1252
            "\x1BNa"  => 'Æ', // 198 in CP1252
            "\x1BNKC" => 'Ç', // 199 in CP1252
            "\x1BNAE" => 'È', // 200 in CP1252
            "\x1BNBE" => 'É', // 201 in CP1252
            "\x1BNCE" => 'Ê', // 202 in CP1252
            "\x1BNHE" => 'Ë', // 203 in CP1252
            "\x1BNAI" => 'Ì', // 204 in CP1252
            "\x1BNBI" => 'Í', // 205 in CP1252
            "\x1BNCI" => 'Î', // 206 in CP1252
            "\x1BNHI" => 'Ï', // 207 in CP1252
            "\x1BNb"  => 'Ð', // 208 in CP1252
            "\x1BNDN" => 'Ñ', // 209 in CP1252
            "\x1BNAO" => 'Ò', // 210 in CP1252
            "\x1BNBO" => 'Ó', // 211 in CP1252
            "\x1BNCO" => 'Ô', // 212 in CP1252
            "\x1BNDO" => 'Õ', // 213 in CP1252
            "\x1BNHO" => 'Ö', // 214 in CP1252
            "\x1B-7"  => '×', // 215 in CP1252
            "\x1BNi"  => 'Ø', // 216 in CP1252
            "\x1BNAU" => 'Ù', // 217 in CP1252
            "\x1BNBU" => 'Ú', // 218 in CP1252
            "\x1BNCU" => 'Û', // 219 in CP1252
            "\x1BNHU" => 'Ü', // 220 in CP1252
            "\x1B-="  => 'Ý', // 221 in CP1252
            "\x1BNl"  => 'Þ', // 222 in CP1252
            "\x1BN{"  => 'ß', // 223 in CP1252
            "\x1BNAa" => 'à', // 224 in CP1252
            "\x1BNBa" => 'á', // 225 in CP1252
            "\x1BNCa" => 'â', // 226 in CP1252
            "\x1BNDa" => 'ã', // 227 in CP1252
            "\x1BNHa" => 'ä', // 228 in CP1252
            "\x1BNJa" => 'å', // 229 in CP1252
            "\x1BNq"  => 'æ', // 230 in CP1252
            "\x1BNKc" => 'ç', // 231 in CP1252
            "\x1BNAe" => 'è', // 232 in CP1252
            "\x1BNBe" => 'é', // 233 in CP1252
            "\x1BNCe" => 'ê', // 234 in CP1252
            "\x1BNHe" => 'ë', // 235 in CP1252
            "\x1BNAi" => 'ì', // 236 in CP1252
            "\x1BNBi" => 'í', // 237 in CP1252
            "\x1BNCi" => 'î', // 238 in CP1252
            "\x1BNHi" => 'ï', // 239 in CP1252
            "\x1BNs"  => 'ð', // 240 in CP1252
            "\x1BNDn" => 'ñ', // 241 in CP1252
            "\x1BNAo" => 'ò', // 242 in CP1252
            "\x1BNBo" => 'ó', // 243 in CP1252
            "\x1BNCo" => 'ô', // 244 in CP1252
            "\x1BNDo" => 'õ', // 245 in CP1252
            "\x1BNHo" => 'ö', // 246 in CP1252
            "\x1B/7"  => '÷', // 247 in CP1252
            "\x1BNy"  => 'ø', // 248 in CP1252
            "\x1BNAu" => 'ù', // 249 in CP1252
            "\x1BNBu" => 'ú', // 250 in CP1252
            "\x1BNCu" => 'û', // 251 in CP1252
            "\x1BNHu" => 'ü', // 252 in CP1252
            "\x1B/="  => 'ý', // 253 in CP1252
            "\x1BN|"  => 'þ', // 254 in CP1252
            "\x1BNHy" => 'ÿ', // 255 in CP1252
        );
    }
}
