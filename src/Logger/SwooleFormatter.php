<?php
namespace DongPHP\Logger;
use Monolog\Formatter\FormatterInterface;

/**
 * Encodes whatever record data is passed to it as json
 *
 * This can be useful to log to databases or remote APIs
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class SwooleFormatter implements FormatterInterface
{
    const BATCH_MODE_JSON = 1;
    const BATCH_MODE_NEWLINES = 2;

    protected $batchMode;
    protected $appendNewline;

    /**
     * @param int $batchMode
     */
    public function __construct($batchMode = self::BATCH_MODE_JSON, $appendNewline = true)
    {
        $this->batchMode = $batchMode;
        $this->appendNewline = $appendNewline;
    }

    public function getBatchMode()
    {
        return $this->batchMode;
    }

    public function isAppendingNewlines()
    {
        return $this->appendNewline;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $send[] = ['filename'=>$record['channel'].'/'.$record['level_name'], 'log'=>$record['message']];
        return json_encode($send) . ($this->appendNewline ? "\r\n" : '');
    }

    /**
     * {@inheritdoc}
     */
    public function formatBatch(array $records)
    {
        switch ($this->batchMode) {
            case static::BATCH_MODE_NEWLINES:
                return $this->formatBatchNewlines($records);

            case static::BATCH_MODE_JSON:
            default:
                return $this->formatBatchJson($records);
        }
    }

    /**
     * Return a JSON-encoded array of records.
     *
     * @param  array  $records
     * @return string
     */
    protected function formatBatchJson(array $records)
    {
        return json_encode($records);
    }

    /**
     * Use new lines to separate records instead of a
     * JSON-encoded array.
     *
     * @param  array  $records
     * @return string
     */
    protected function formatBatchNewlines(array $records)
    {
        $instance = $this;

        $oldNewline = $this->appendNewline;
        $this->appendNewline = false;
        array_walk($records, function (&$value, $key) use ($instance) {
            $value = $instance->format($value);
        });
        $this->appendNewline = $oldNewline;

        return implode("\r\n", $records);
    }
}
