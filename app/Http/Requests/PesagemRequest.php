<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Coleta;
use App\Models\Tipo;

class PesagemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'coleta_id' => [
                'required',
                'exists:coletas,id',
                function ($attribute, $value, $fail) {
                    $coleta = Coleta::find($value);
                    if ($coleta && !$coleta->podeReceberPesagens()) {
                        $fail('Esta coleta não pode receber pesagens no status atual.');
                    }
                },
            ],
            'tipo_id' => 'required|exists:tipos,id',
            'peso' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    // Validar se o peso não é muito diferente do esperado
                    if ($this->coleta_id && $this->tipo_id) {
                        $coleta = Coleta::find($this->coleta_id);
                        $tipo = Tipo::find($this->tipo_id);
                        
                        if ($coleta && $tipo) {
                            $pecaColeta = $coleta->pecas()->where('tipo_id', $this->tipo_id)->first();
                            if ($pecaColeta) {
                                $pesoEsperado = $pecaColeta->peso;
                                $diferenca = abs($value - $pesoEsperado) / $pesoEsperado * 100;
                                
                                // Alerta se diferença for maior que 50%
                                if ($diferenca > 50) {
                                    $fail("O peso informado ({$value} kg) difere significativamente do peso esperado ({$pesoEsperado} kg). Verifique se está correto.");
                                }
                            }
                        }
                    }
                },
            ],
            'quantidade' => [
                'required',
                'integer',
                'min:1',
                'max:999',
                function ($attribute, $value, $fail) {
                    // Validar se a quantidade não excede muito a esperada
                    if ($this->coleta_id && $this->tipo_id) {
                        $coleta = Coleta::find($this->coleta_id);
                        
                        if ($coleta) {
                            $pecaColeta = $coleta->pecas()->where('tipo_id', $this->tipo_id)->first();
                            if ($pecaColeta && $value > ($pecaColeta->quantidade * 2)) {
                                $fail("A quantidade informada ({$value}) é muito maior que a esperada ({$pecaColeta->quantidade}). Verifique se está correto.");
                            }
                        }
                    }
                },
            ],
            'data_pesagem' => [
                'required',
                'date',
                'before_or_equal:now',
                function ($attribute, $value, $fail) {
                    // Validar se a data não é muito antiga
                    $dataPesagem = \Carbon\Carbon::parse($value);
                    $diasAtras = now()->diffInDays($dataPesagem);
                    
                    if ($diasAtras > 30) {
                        $fail('A data da pesagem não pode ser superior a 30 dias atrás.');
                    }
                },
            ],
            'local_pesagem' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:rascunho,concluida',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'coleta_id.required' => 'Selecione uma coleta.',
            'coleta_id.exists' => 'Coleta inválida.',
            'tipo_id.required' => 'Selecione um tipo de peça.',
            'tipo_id.exists' => 'Tipo de peça inválido.',
            'peso.required' => 'Informe o peso.',
            'peso.numeric' => 'O peso deve ser um número.',
            'peso.min' => 'O peso deve ser maior que 0.',
            'peso.max' => 'O peso deve ser menor que 1000 kg.',
            'quantidade.required' => 'Informe a quantidade.',
            'quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade deve ser pelo menos 1.',
            'quantidade.max' => 'A quantidade deve ser menor que 1000.',
            'data_pesagem.required' => 'Informe a data da pesagem.',
            'data_pesagem.date' => 'Data de pesagem inválida.',
            'data_pesagem.before_or_equal' => 'A data da pesagem não pode ser futura.',
            'local_pesagem.max' => 'O local deve ter no máximo 255 caracteres.',
            'observacoes.max' => 'As observações devem ter no máximo 1000 caracteres.',
            'status.in' => 'Status inválido. Deve ser rascunho ou concluída.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'coleta_id' => 'coleta',
            'tipo_id' => 'tipo de peça',
            'peso' => 'peso',
            'quantidade' => 'quantidade',
            'data_pesagem' => 'data da pesagem',
            'local_pesagem' => 'local da pesagem',
            'observacoes' => 'observações',
            'status' => 'status',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Converter vírgula para ponto no peso
        if ($this->has('peso')) {
            $this->merge([
                'peso' => str_replace(',', '.', $this->peso),
            ]);
        }
    }
}
