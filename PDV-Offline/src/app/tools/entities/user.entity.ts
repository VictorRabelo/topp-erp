import { Entity, PrimaryGeneratedColumn, Column, BaseEntity, CreateDateColumn, UpdateDateColumn } from "typeorm";

@Entity({ name: 'users' })
export class User extends BaseEntity {

    @PrimaryGeneratedColumn()
    id: string;

    @Column('varchar', { nullable: true })
    empresa_id: string;

    @Column('varchar', { nullable: true })
    nome: string;

    @Column('varchar', { nullable: true })
    sobrenome: string;

    @Column('int', { default: 1 })
    status: string;

    @Column('varchar', { nullable: true })
    email: string;

    @Column('varchar', { nullable: true })
    email_verified_at: string;

    @Column('varchar', { nullable: true })
    password: string;

    @Column('varchar', { nullable: true })
    permissions: string;

    @Column('varchar', { nullable: true })
    remember_token: string;

    @CreateDateColumn({ name: 'created_at' }) 'created_at': Date;
    @UpdateDateColumn({ name: 'updated_at' }) 'updated_at': Date;

}