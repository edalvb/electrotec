import { Button, Card, Flex, Heading, Text } from '@radix-ui/themes'

export default function Page() {
  return (
    <div className="min-h-screen p-6 flex items-center justify-center">
      <div className="w-full max-w-2xl space-y-6">
        <div className="flex justify-center">
          <Heading size="7">Nuevo Certificado</Heading>
        </div>
        <Card className="glass p-6">
          <Flex direction="column" gap="4">
            <Text size="4">Próximamente podrás crear certificados desde aquí.</Text>
            <Button disabled className="btn-primary">Crear</Button>
          </Flex>
        </Card>
      </div>
    </div>
  )
}
