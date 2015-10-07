<?php

class DbMessageSource extends CDbMessageSource
{
    /**
     * @var mixed what to do when wanted translation is missing:
     *
     * - 'source' (default) return message source if translation not found.
     * - 'langFallback' try to fall back on secondary language option, e.g. `pt_br` => `pt`, and return source message if fallback cannot be found.
     * - null return null if translation cannot be found.
     */
    public $missingTranslationAction = 'source';

    /**
     * @var array runtime cache for translation strings.
     */
    protected $loadedMessages = array();

    /**
     * Translates the specified message.
     *
     * If the message is not found, an {@link onMissingTranslation} event will be raised.
     *
     * @param string $category the category that the message belongs to
     * @param string $message the message to be translated
     * @param string $language the target language
     * @return string the translated message
     */
    protected function translateMessage($category, $message, $language)
    {
        $key = $language . '.' . $category;

        if (!isset($this->loadedMessages[$key])) {
            $this->loadedMessages[$key] = $this->loadMessages($category, $language);
        }
        if (isset($this->loadedMessages[$key][$message]) && $this->loadedMessages[$key][$message] !== '') {
            return $this->loadedMessages[$key][$message];
        }

        switch ($this->missingTranslationAction) {
            case null:
                return null;

            // Note that this falls back on the default case, i.e. returns the source message.
            case 'langFallback':
                if (strlen($language) === 5) {
                    $fallbackLanguage = substr($language, 0, 2);
                    $key = $fallbackLanguage . '.' . $category;

                    if (!isset($this->loadedMessages[$key])) {
                        $this->loadedMessages[$key] = $this->loadMessages($category, $fallbackLanguage);
                    }
                    if (isset($this->loadedMessages[$key][$message]) && $this->loadedMessages[$key][$message] !== '') {
                        return $this->loadedMessages[$key][$message];
                    }
                }

            // The default case and `source` both return the source message.
            case 'source':
            default:
                return $message;
        }
    }
} 