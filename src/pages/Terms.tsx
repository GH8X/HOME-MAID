import { LegalPage } from './Legal';
import { TERMS } from '../data/pages';

export function Terms() {
  return <LegalPage content={TERMS} path="/terms" crumbLabel="Terms & conditions" />;
}
