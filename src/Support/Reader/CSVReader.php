<?php

namespace AlgoYounes\CommissionTask\Support\Reader;

use AlgoYounes\CommissionTask\Entity\Operation;
use Generator;
use InvalidArgumentException;
use SplFileObject;

final class CSVReader
{
    private ?string $objectClass = null;

    private function __construct(private SplFileObject $fileObject)
    {
        $this->fileObject->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
    }

    public static function fromFilePath(string $filePath): self
    {
        $fileObject = new SplFileObject($filePath);

        return new self($fileObject);
    }

    public function withObject(string $objectClass): self
    {
        if (! method_exists($objectClass, 'fromArray')) {
            throw new InvalidArgumentException("Class {$objectClass} must have a static fromArray(array \$data) method.");
        }

        $this->objectClass = $objectClass;

        return $this;
    }

    /**
     * @return Generator|Operation[]
     */
    public function read(): Generator
    {
        $this->fileObject->rewind();

        while (($data = $this->fileObject->fgetcsv()) !== false) {
            if (empty($data[0])) {
                continue;
            }

            if ($this->objectClass !== null) {
                $data = $this->objectClass::fromArray($data);
            }

            yield $data;
        }
    }
}
