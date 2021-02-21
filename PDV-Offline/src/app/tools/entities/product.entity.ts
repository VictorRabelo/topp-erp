import { Entity, PrimaryGeneratedColumn, Column, BaseEntity, CreateDateColumn, UpdateDateColumn } from "typeorm";

@Entity({ name: 'produtos' })
export class Product extends BaseEntity {

	@PrimaryGeneratedColumn()
	id: string;

	@Column('int', { nullable: true })
	empresa_id: string;

	@Column('int', { default: 1 })
	tipo: string;

	@Column('varchar', { nullable: true })
	codigo_barras: string;

	@Column('varchar', { nullable: true })
	referencia: string;

	@Column('varchar')
	descricao: string;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	custo: string;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	margem: string;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	preco: string;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	estoque: string;

	@Column('varchar', { default: 'UN' })
	medida: string;

	@Column('int', { default: 0 })
	origin: string;

	@Column('varchar', { nullable: true })
	ncm: string;

	@Column('varchar', { default: '5102' })
	cfop: string;

	@Column('varchar', { default: '102' })
	cst_icms: string;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	p_icms: string;

	@Column('varchar', { default: '50' })
	cst_ipi: string;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	p_ipi: string;

	@Column('varchar', { default: '07' })
	cst_pis: string;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	p_pis: string;

	@Column('varchar', { default: '07' })
	cst_cofins: string;

	@Column('float', { default: 0, precision: 10, scale: 2 })
	p_cofins: string;

	@Column('text', { nullable: true })
	foto: string;

	@CreateDateColumn({ name: 'created_at' }) 'created_at': Date;
	@UpdateDateColumn({ name: 'updated_at' }) 'updated_at': Date;

}