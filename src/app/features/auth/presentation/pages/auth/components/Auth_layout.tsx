'use client'
import { useAuthState } from '../auth_states'
import { useAuthContext } from '../Auth_context'
import { Button, Card, Flex, Heading, Text } from '@radix-ui/themes'
import type { ChangeEvent } from 'react'

export default function AuthLayout() {
  const { email, password, isLoading, error, setEmail, setPassword } = useAuthState(s => s)
  const { controller } = useAuthContext()
  return (
    <div className="min-h-screen flex items-center justify-center p-6">
      <Card className="glass max-w-md w-full p-6">
        <Flex direction="column" gap="4">
          <Heading size="6">Ingresar</Heading>
          <Flex direction="column" gap="2">
            <Text>Email</Text>
            <input value={email} onChange={(e: ChangeEvent<HTMLInputElement>) => setEmail(e.target.value)} className="input-glass rounded-md px-3 py-2" type="email" placeholder="correo@dominio.com" />
          </Flex>
          <Flex direction="column" gap="2">
            <Text>Contraseña</Text>
            <input value={password} onChange={(e: ChangeEvent<HTMLInputElement>) => setPassword(e.target.value)} className="input-glass rounded-md px-3 py-2" type="password" placeholder="••••••••" />
          </Flex>
          {error ? <Text color="red">{error}</Text> : null}
          <Button className="btn-primary" disabled={isLoading} onClick={controller.handleLogin}>{isLoading ? 'Ingresando…' : 'Ingresar'}</Button>
        </Flex>
      </Card>
    </div>
  )
}
