'use client'
import { supabaseBrowser } from '@/lib/supabase/client'

export class AuthRepository {
  async signIn(email: string, password: string){
  const { error } = await supabaseBrowser().auth.signInWithPassword({ email, password })
    if (error) return { ok: false, message: error.message }
    return { ok: true }
  }
}
