<?php

namespace App\Filters;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class DocumentBuilder extends BaseModelBuilder
{

    protected function getModelClass(): string
    {
        return DocumentBuilder::class;
    }
    public function whereDocumentType(string $value = null): static
    {
        if ($value == null) return $this;
        $this->where('document_type', $value);
        return $this;
    }

    public function whereStatus(string $value = null): static
    {
        if ($value == null) return $this;
        $this->where('status', $value);
        return $this;
    }

}
