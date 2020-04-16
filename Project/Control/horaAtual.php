<?php

class horaAtual
{
			
			private $data;
			private $hora;
			private $minutos;
			private $segundos;
			private $horaAtual;
			
			public function setHoraAgora()
			{
				$this->data=date('d/m/Y');
				$this->hora=date('H');
			    $this->minutos=date('i');
			    $this->segundos=date('s');
			}
			
			public function horaAgora()
			{
				$this->setHoraAgora();
		
					  if($this->hora>=12 && $this->hora<18)
					  {
						return("Boa Tarde, hoje é $this->data - $this->hora:$this->minutos:$this->segundos");
					  }
					  if($this->hora>=18 && $this->hora<24)
					  {
						return("Boa Noite, hoje é $this->data - $this->hora:$this->minutos:$this->segundos");
					  }
					  if($this->hora>=00 && $this->hora<12)
					  {
						return("Bom Dia, hoje é $this->data - $this->hora:$this->minutos:$this->segundos");
					  }
			}
}
?>
