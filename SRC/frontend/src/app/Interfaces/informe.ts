export interface Informe {
   
    id_informe : number;
    nombre_paciente: string;
    dni_paciente:string;
    fecha : string;
    url_archivo : string;
    mail_paciente : string;
    tipo_informe : string;
    id_cobertura? : number;
    fecha_nacimiento_paciente?: string;
    numero_afiliado?: number;
    medico_envia_estudio?: string;
    motivo_estudio? : string;
    estomago?:string;
    duodeno? : string;
    esofago? : string;
    conclusion? : string;
    efectuo_terapeutica? : number;
    tipo_terapeutica?: string;
    efectuo_biopsia?: number;
    fracos_biopsia?: number;
    informe?: string;
    edad? : number;

}
export interface InformeUpdate {
  nombre_paciente?: string;
  dni_paciente?: string;
  fecha?: string;
  url_archivo?: string;
  mail_paciente?: string;
  tipo_informe?: string;
  id_cobertura?: number;
  fecha_nacimiento_paciente?: string;
  numero_afiliado?: string;
  medico_envia_estudio?: string;
  motivo_estudio?: string;
  estomago?: string;
  duodeno?: string;
  esofago?: string;
  conclusion?: string;
  efectuo_terapeutica?: number;
  tipo_terapeutica?: string | null;
  efectuo_biopsia?: number;
  fracos_biopsia?: number | null;
  informe?: string;
  edad?: number;
 
}