<?php

namespace WPMultiLanguage;

class TranslationService
{
    public function update($type, $entityId, $lang, $field, $value)
    {
        global $wpdb;
        $result = $wpdb->query($wpdb->prepare(
            "INSERT INTO {$wpdb->prefix}{$type}_translations (entity_id, lang, field, `value`)
                VALUES ('%d', '%s', '%s', '%s')
            ON DUPLICATE KEY UPDATE value = '%s'",
            $entityId,
            $lang,
            $field,
            $value,
            $value
        ));
    }

    /**
     * @param string $type
     * @param int $entityId
     * @param $lang
     * @return array|null
     */
    public function getTranslationsEntity($type, $entityId, $lang)
    {
        global $wpdb;
        $result = $wpdb->get_results($wpdb->prepare(
            "SELECT * from {$wpdb->prefix}{$type}_translations
            WHERE entity_id = '%d' AND lang = '%s'",
            $entityId,
            $lang
        ), ARRAY_A);
        $translations = [];

        foreach ($result as $row) {
            $translations[$row['field']] = $row['value'];
        }

        return $translations;
    }

    /**
     * @param string $type
     * @param array $entityIds
     * @return array|null
     */
    public function getTranslationsEntities($type, array $entityIds, $lang)
    {
        global $wpdb;
        $result = $wpdb->get_results($wpdb->prepare(
            "SELECT * from {$wpdb->prefix}{$type}_translations
            WHERE entity_id IN ('%s') AND lang = '%s'",
            implode("', '", $entityIds),
            $lang
        ), ARRAY_A);
        $translations = [];

        foreach ($entityIds as $entityId) {
            $translations[$entityId] = [];
        }
        foreach ($result as $row) {
            $translations[$row['entity_id']][$row['field']] = $row['value'];
        }

        return $translations;
    }
}