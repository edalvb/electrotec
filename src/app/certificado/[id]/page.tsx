import CertificatePublicView from '@/app/features/certificate_public/presentation/pages/certificate_public/Certificate_public_view'

export default async function Page(props: { params: Promise<{ id: string }> }) {
	const { id } = await props.params
	return <CertificatePublicView id={id}/>
}
